<?php

namespace UisIts\Oidc\Providers;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use UisIts\Oidc\Exceptions\AuthenticationException;
use UisIts\Oidc\Exceptions\InvalidLogoutException;
use UisIts\Oidc\Exceptions\InvalidStateException;
use UisIts\Oidc\Exceptions\ValueNotFoundException;
use UisIts\Oidc\User;

class UisOidcProvider extends AbstractOidcProvider
{
    /**
     * The separating character for the requested scopes.
     *
     * @var string
     */
    protected string $scopeSeparator = ' ';

    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected array $scopes = [
        'openid',
        'profile',
        'email',
        'phone',
        'address',
        'offline_access',
    ];

    /**
     * @var bool
     */
    protected bool $usesPKCE = true;

    /**
     * The cached user instance.
     *
     * @var User|null
     */
    protected ?User $user = null;

    /**
     * Set the scopes
     *
     * @return array
     * @throws ValueNotFoundException
     */
    public function getScopes(): array
    {
        if (empty(config('shibboleth-oidc.providers.uis.scopes'))) {
            throw new ValueNotFoundException('Scopes not set in config file');
        }

        return array_unique((array)config('shibboleth-oidc.providers.uis.scopes'));
    }

    /**
     * Returns the auth url for authentication provider.
     * @throws ValueNotFoundException
     */
    protected function getAuthUrl($state): string
    {
        if (empty(config('shibboleth-oidc.providers.uis.auth_url'))) {
            throw new ValueNotFoundException('Auth url not set in config');
        }

        return $this->buildAuthUrlFromBase(
            config('shibboleth-oidc.providers.uis.auth_url'),
            $state
        );
    }

    /**
     * @param $code
     * @return array
     *
     * @throws ValueNotFoundException
     */
    public function getAccessTokenResponse($code): array
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            RequestOptions::AUTH => [$this->clientId, $this->clientSecret],
            RequestOptions::FORM_PARAMS => $this->getTokenFields($code),
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     * @throws ValueNotFoundException
     */
    protected function getTokenUrl(): string
    {
        if (empty(config('shibboleth-oidc.providers.uis.token_url'))) {
            throw new ValueNotFoundException('UIS token url not set in config');
        }

        return config('shibboleth-oidc.providers.uis.token_url');
    }

    /**
     * Get the url to retrieve user by token
     *
     * @return string|null
     * @throws ValueNotFoundException
     */
    protected function getUserUrl(): ?string
    {
        if (empty(config('shibboleth-oidc.providers.uis.user_url'))) {
            throw new ValueNotFoundException('UIS User profile url not set in config');
        }

        return config('shibboleth-oidc.providers.uis.user_url');
    }

    /**
     * Get the url to introspect user token
     * @throws ValueNotFoundException
     */
    protected function getIntrospectUrl(): string
    {
        if (empty(config('shibboleth-oidc.providers.uis.introspect_url'))) {
            throw new ValueNotFoundException('Introspect url not set in config');
        }

        return config('shibboleth-oidc.providers.uis.introspect_url');
    }

    /**
     * {@inheritdoc}
     * @throws ValueNotFoundException|GuzzleException
     */
    public function getUserByToken($token): array
    {
        $response = $this->getHttpClient()->get($this->getUserUrl(), [
            RequestOptions::HEADERS => ['Authorization' => 'Bearer ' . $token],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCodeFields($state = null): array
    {
        $fields = parent::getCodeFields($state);

        if ($this->isStateless()) {
            $fields['state'] = 'state';
        }

        return $fields;
    }

    /**
     * {@inheritdoc}
     *
     * @throws GuzzleException|ValueNotFoundException
     * @throws InvalidStateException
     */
    public function user(): User
    {
        if ($this->user) {
            return $this->user;
        }

        if ($this->hasInvalidState()) {
            throw new InvalidStateException('Invalid state');
        }

        $response = $this->getAccessTokenResponse($this->getCode());

        $this->user = $this->mapUserToObject($this->getUserByToken(
            $token = Arr::get($response, 'access_token')
        ));

        return $this->user->setToken($token)
            ->setIdToken(Arr::get($response, 'id_token'))
            ->setRefreshToken(Arr::get($response, 'refresh_token'))
            ->setExpiresIn(Arr::get($response, 'expires_in'))
            ->setApprovedScopes(explode($this->scopeSeparator, Arr::get($response, 'scope', '')));
    }

    protected function mapUserToObject(array $user): User
    {
        return (new User)->setRaw($user)->map([
            'uin' => $user[config('shibboleth-oidc.providers.uis.user-mapping.uin')],
            'netid' => $user[config('shibboleth-oidc.providers.uis.user-mapping.netid')],
            'firstName' => $user[config('shibboleth-oidc.providers.uis.user-mapping.first_name')],
            'lastName' => $user[config('shibboleth-oidc.providers.uis.user-mapping.last_name')],
            'name' => $user['given_name'] . ' ' . $user['family_name'],
            'email' => $user[config('shibboleth-oidc.providers.uis.user-mapping.email')],
            'password' => Hash::make($user[config('shibboleth-oidc.providers.uis.user-mapping.uin')] . now()),
            'groups' => $user['uisedu_is_member_of'],
        ]);
    }

    /**
     * Introspect the user token
     *
     * @return array|mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function introspect($token): mixed
    {
        $clientId = config('shibboleth-oidc.uis-provider.introspect.client_id');
        $clientSecret = config('shibboleth-oidc.uis-provider.introspect.client_secret');

        throw_if(empty($clientId) || empty($clientSecret), new ValueNotFoundException('UIS Introspect Client ID or Secret not set!'));

        $response = $this->getHttpClient()->post(
            $this->getIntrospectUrl(), [
            RequestOptions::FORM_PARAMS => [
                'token' => $token,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Logout currently authenticated User
     *
     * @throws \Throwable
     */
    public function logout(): RedirectResponse
    {
        $user = Auth::user();
        throw_if(!$user, AuthenticationException::class);
        $logout_url = config('shibboleth-oidc.providers.uis.logout_url');
        $response = $this->getHttpClient()->get($logout_url, [
            RequestOptions::HEADERS => ['Authorization' => 'Bearer ' . $user->access_token],
        ]);

        if ($response->getStatusCode() === 200) {
            Auth::logout();
            Session::flush();

            return new RedirectResponse(config('shibboleth-oidc.providers.uis.logout_url'));
        }

        throw new InvalidLogoutException('User logout failed!');
    }
}
