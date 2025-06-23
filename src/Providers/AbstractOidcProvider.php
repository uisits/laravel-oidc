<?php

namespace UisIts\Oidc\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use UisIts\Oidc\Contracts\Provider as ProviderContract;
use UisIts\Oidc\Exceptions\InvalidStateException;
use UisIts\Oidc\Token;
use UisIts\Oidc\User;

abstract class AbstractOidcProvider implements ProviderContract
{
    /**
     * The HTTP request instance.
     */
    protected Request $request;

    /**
     * The HTTP Client instance.
     */
    protected ?Client $httpClient = null;

    /**
     * The client ID.
     */
    protected string $clientId;

    /**
     * The client secret.
     */
    protected string $clientSecret;

    /**
     * The redirect URL.
     */
    protected string $redirectUrl;

    /**
     * The custom parameters to be sent with the request.
     */
    protected array $parameters = [];

    /**
     * The scopes being requested.
     */
    protected array $scopes = [];

    /**
     * The separating character for the requested scopes.
     */
    protected string $scopeSeparator = ',';

    /**
     * The type of the encoding in the query.
     *
     * @var int Can be either PHP_QUERY_RFC3986 or PHP_QUERY_RFC1738.
     */
    protected int $encodingType = PHP_QUERY_RFC1738;

    /**
     * Indicates if the session state should be used.
     */
    protected bool $stateless = false;

    /**
     * Indicates if PKCE should be used.
     */
    protected bool $usesPKCE = false;

    /**
     * The custom Guzzle configuration options.
     */
    protected array $guzzle = [];

    /**
     * The cached user instance.
     */
    protected ?User $user;

    protected ?Collection $openIdConfig = null;

    /**
     * Create a new provider instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        string $clientId,
        string $clientSecret,
        string $redirectUrl,
        array $guzzle = []
    ) {
        $this->guzzle = $guzzle;
        $this->request = $request;
        $this->clientId = $clientId;
        $this->redirectUrl = $redirectUrl;
        $this->clientSecret = $clientSecret;
        $this->openIdConfig = collect();
    }

    /**
     * Get the authentication URL for the provider.
     */
    abstract protected function getAuthUrl(string $state): string;

    /**
     * Get the token URL for the provider.
     */
    abstract protected function getTokenUrl(): string;

    /**
     * Get the raw user for the given access token.
     */
    abstract protected function getUserByToken(string $token): array;

    /**
     * Map the raw user array to a User instance.
     */
    abstract protected function mapUserToObject(array $user): User;

    /**
     * Redirect the user of the application to the provider's authentication screen.
     */
    public function redirect(): RedirectResponse
    {
        $state = null;

        if ($this->usesState()) {
            $this->request->session()->put('state', $state = $this->getState());
        }

        if ($this->usesPKCE()) {
            $this->request->session()->put('code_verifier', $this->getCodeVerifier());
        }

        return new RedirectResponse($this->getAuthUrl($state));
    }

    /**
     * Build the authentication URL for the provider from the given base URL.
     */
    protected function buildAuthUrlFromBase(string $url, string $state): string
    {
        return $url.'?'.http_build_query($this->getCodeFields($state), '', '&', $this->encodingType);
    }

    /**
     * Get the GET parameters for the code request.
     */
    protected function getCodeFields(?string $state = null): array
    {
        $fields = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'scope' => $this->formatScopes($this->getScopes(), $this->scopeSeparator),
            'response_type' => 'code',
        ];

        if ($this->usesState()) {
            $fields['state'] = $state;
        }

        if ($this->usesPKCE()) {
            $fields['code_challenge'] = $this->getCodeChallenge();
            $fields['code_challenge_method'] = $this->getCodeChallengeMethod();
        }

        return array_merge($fields, $this->parameters);
    }

    /**
     * Format the given scopes.
     */
    protected function formatScopes(array $scopes, string $scopeSeparator): string
    {
        return implode($scopeSeparator, $scopes);
    }

    /**
     * Get the user from the provider.
     *
     * @throws InvalidStateException|GuzzleException
     */
    public function user(): User
    {
        if ($this->user) {
            return $this->user;
        }

        if ($this->hasInvalidState()) {
            throw new InvalidStateException;
        }

        $response = $this->getAccessTokenResponse($this->getCode());

        $user = $this->getUserByToken(Arr::get($response, 'access_token'));

        return $this->userInstance($response, $user);
    }

    /**
     * Create a user instance from the given data.
     */
    protected function userInstance(array $response, array $user): User
    {
        $this->user = $this->mapUserToObject($user);

        return $this->user->setToken(Arr::get($response, 'access_token'))
            ->setRefreshToken(Arr::get($response, 'refresh_token'))
            ->setExpiresIn(Arr::get($response, 'expires_in'))
            ->setApprovedScopes(explode($this->scopeSeparator, Arr::get($response, 'scope', '')));
    }

    /**
     * Get a User instance from a known access token.
     */
    public function userFromToken(string $token): User
    {
        $user = $this->mapUserToObject($this->getUserByToken($token));

        return $user->setToken($token);
    }

    /**
     * Determine if the current request / session has a mismatching "state".
     */
    protected function hasInvalidState(): bool
    {
        if ($this->isStateless()) {
            return false;
        }

        $state = $this->request->session()->pull('state');

        return empty($state) || $this->request->input('state') !== $state;
    }

    /**
     * Get the access token response for the given code.
     *
     * @throws GuzzleException
     */
    public function getAccessTokenResponse(string $code): array
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            RequestOptions::HEADERS => $this->getTokenHeaders($code),
            RequestOptions::FORM_PARAMS => $this->getTokenFields($code),
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Get the headers for the access token request.
     */
    protected function getTokenHeaders(string $code): array
    {
        return ['Accept' => 'application/json'];
    }

    /**
     * Get the POST fields for the token request.
     */
    protected function getTokenFields(string $code): array
    {
        $fields = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'redirect_uri' => $this->redirectUrl,
        ];

        if ($this->usesPKCE()) {
            $fields['code_verifier'] = $this->request->session()->pull('code_verifier');
        }

        return array_merge($fields, $this->parameters);
    }

    /**
     * Refresh a user's access token with a refresh token.
     */
    public function refreshToken(string $refreshToken): Token
    {
        $response = $this->getRefreshTokenResponse($refreshToken);

        return new Token(
            Arr::get($response, 'access_token'),
            Arr::get($response, 'refresh_token'),
            Arr::get($response, 'expires_in'),
            explode($this->scopeSeparator, Arr::get($response, 'scope', ''))
        );
    }

    /**
     * Get the refresh token response for the given refresh token.
     */
    protected function getRefreshTokenResponse(string $refreshToken): array
    {
        return json_decode($this->getHttpClient()->post($this->getTokenUrl(), [
            RequestOptions::HEADERS => ['Accept' => 'application/json'],
            RequestOptions::FORM_PARAMS => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ],
        ])->getBody(), true);
    }

    /**
     * Get the code from the request.
     */
    protected function getCode(): string
    {
        return $this->request->input('code');
    }

    /**
     * Merge the scopes of the requested access.
     *
     * @return $this
     */
    public function scopes(array|string $scopes): static
    {
        $this->scopes = array_values(array_unique(array_merge($this->scopes, (array) $scopes)));

        return $this;
    }

    /**
     * Set the scopes of the requested access.
     *
     * @return $this
     */
    public function setScopes(array|string $scopes): static
    {
        $this->scopes = array_values(array_unique((array) $scopes));

        return $this;
    }

    /**
     * Get the current scopes.
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    /**
     * Set the redirect URL.
     *
     * @return $this
     */
    public function redirectUrl(string $url): static
    {
        $this->redirectUrl = $url;

        return $this;
    }

    /**
     * Get an instance of the Guzzle HTTP client.
     */
    protected function getHttpClient(): Client
    {
        if (is_null($this->httpClient)) {
            $this->httpClient = new Client($this->guzzle);
        }

        return $this->httpClient;
    }

    /**
     * Set the Guzzle HTTP client instance.
     *
     * @return $this
     */
    public function setHttpClient(Client $client): static
    {
        $this->httpClient = $client;

        return $this;
    }

    /**
     * Set the request instance.
     *
     * @return $this
     */
    public function setRequest(Request $request): static
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Determine if the provider is operating with a state.
     */
    protected function usesState(): bool
    {
        return ! $this->stateless;
    }

    /**
     * Determine if the provider is operating as stateless.
     */
    protected function isStateless(): bool
    {
        return $this->stateless;
    }

    /**
     * Indicates that the provider should operate as stateless.
     *
     * @return $this
     */
    public function stateless(): static
    {
        $this->stateless = true;

        return $this;
    }

    /**
     * Get the string used for session state.
     */
    protected function getState(): string
    {
        return Str::random(40);
    }

    /**
     * Determine if the provider uses PKCE.
     */
    protected function usesPKCE(): bool
    {
        return $this->usesPKCE;
    }

    /**
     * Enables PKCE for the provider.
     *
     * @return $this
     */
    public function enablePKCE(): static
    {
        $this->usesPKCE = true;

        return $this;
    }

    /**
     * Generates a random string of the right length for the PKCE code verifier.
     */
    protected function getCodeVerifier(): string
    {
        return Str::random(96);
    }

    /**
     * Generates the PKCE code challenge based on the PKCE code verifier in the session.
     */
    protected function getCodeChallenge(): string
    {
        $hashed = hash('sha256', $this->request->session()->get('code_verifier'), true);

        return rtrim(strtr(base64_encode($hashed), '+/', '-_'), '=');
    }

    /**
     * Returns the hash method used to calculate the PKCE code challenge.
     */
    protected function getCodeChallengeMethod(): string
    {
        return 'S256';
    }

    /**
     * Set the custom parameters of the request.
     *
     * @return $this
     */
    public function with(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }
}
