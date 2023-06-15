<?php

namespace UisIts\Oidc\Oidc;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\ProviderInterface;

final class ShibbolethOidcProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * The separating character for the requested scopes.
     *
     * @var string
     */
    protected $scopeSeparator = ' ';

    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = [
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
    protected $usesPKCE = true;

    /**
     * The cached user instance.
     *
     * @var \UisIts\Oidc\Oidc\User|null
     */
    protected $user;

    /**
     * Set the scopes
     *
     * @return array
     */
    public function getScopes()
    {
        if (empty(config('shibboleth.oidc.scopes'))) {
            throw new \ValueError('Scopes not set in config file');
        }

        return array_unique((array) config('shibboleth.oidc.scopes'));
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state): string
    {
        if (empty(config('shibboleth.oidc.auth_url'))) {
            throw new \ValueError('auth url not set in config');
        }

        return $this->buildAuthUrlFromBase(config('shibboleth.oidc.auth_url'), $state);
    }

    /**
     * @return array|mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAccessTokenResponse($code): mixed
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            RequestOptions::AUTH => [$this->clientId, $this->clientSecret],
            RequestOptions::FORM_PARAMS => $this->getTokenFields($code),
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        if (empty(config('shibboleth.oidc.token_url'))) {
            throw new \ValueError('token url not set in config');
        }

        return config('shibboleth.oidc.token_url');
    }

    /**
     * Get the url to retrieve user by token
     *
     * @return string|null
     */
    protected function getUserUrl()
    {
        if (empty(config('shibboleth.oidc.user_url'))) {
            throw new \ValueError('User profile url not set in config');
        }

        return config('shibboleth.oidc.user_url');
    }

    /**
     * Get the url to introspect user token
     */
    protected function getIntrospectUrl(): string
    {
        if (empty(config('shibboleth.introspect.introspect_url'))) {
            throw new \ValueError('Introspect url not set in config');
        }

        return config('shibboleth.introspect.introspect_url');
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get($this->getUserUrl(), [
            RequestOptions::HEADERS => ['Authorization' => 'Bearer '.$token],
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
     * @throws GuzzleException
     */
    public function user()
    {
        if ($this->user) {
            return $this->user;
        }

        if ($this->hasInvalidState()) {
            throw new InvalidStateException;
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
            'uin' => $user['uisedu_uin'],
            'netid' => $user['preferred_username'],
            'first_name' => $user['given_name'],
            'last_name' => $user['family_name'],
            'name' => $user['given_name'].' '.$user['family_name'],
            'email' => $user['email'],
            'password' => Hash::make($user['uisedu_uin'].now()),
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
        $clientId = config('shibboleth.introspect.client_id');
        $clientSecret = config('shibboleth.introspect.client_secret');
        throw_if(empty($clientId), new Exception('Introspect Client ID not set!'));
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
        throw_if(! $user, AuthenticationException::class);
        $logout_url = config('shibboleth.oidc.logout_url');
        $response = $this->getHttpClient()->get($logout_url, [
            RequestOptions::HEADERS => ['Authorization' => 'Bearer '.$user->access_token],
        ]);

        if ($response->getStatusCode() === 200) {
            Auth::logout();
            Session::flush();

            return new RedirectResponse(config('shibboleth.oidc.logout_url'));
        }

        throw new \Exception('Could not Logout User!');
    }
}
