<?php

namespace PrasadChinwal\Shibboleth\Test\stubs;

use GuzzleHttp\RequestOptions;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;
use Mockery as m;
use stdClass;

class OidcProviderStub extends AbstractProvider
{
    /**
     * @var \GuzzleHttp\Client|\Mockery\MockInterface
     */
    public $http;

    protected $usesPKCE = true;

    protected $scopeSeparator = ' ';

    protected $scopes = [
        'openid',
        'profile',
        'email',
        'phone',
        'address',
        'offline_access',
    ];

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

    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('http://auth.url', $state);
    }

    protected function getTokenUrl()
    {
        return 'http://token.url';
    }

    protected function getUserByToken($token)
    {
        return [
            'uisedu_uin' => '123456789',
            'preferred_username' => 'abc',
            'given_name' => 'first',
            'family_name' => 'last',
            'email' => 'abc@xxx.org',
            'groups' => ['test-group', 'app2', 'app3', 'app4'],
        ];
    }

    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'uin' => $user['uisedu_uin'],
            'netid' => $user['preferred_username'],
            'first_name' => $user['given_name'],
            'last_name' => $user['family_name'],
            'name' => $user['given_name'].' '.$user['family_name'],
            'email' => $user['email'],
            'groups' => $user['groups'],
        ]);
    }

    /**
     * Get a fresh instance of the Guzzle HTTP client.
     *
     * @return \GuzzleHttp\Client|\Mockery\MockInterface
     */
    protected function getHttpClient()
    {
        if ($this->http) {
            return $this->http;
        }

        return $this->http = m::mock(stdClass::class);
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
            RequestOptions::HEADERS => ['Authorization' => 'Bearer '.$user->token],
        ]);

        if ($response->getStatusCode() === 200) {
            Auth::logout();
            Session::flush();

            return new RedirectResponse(config('shibboleth.oidc.logout_url'));
        }

        throw new \Exception('Could not Logout User!');
    }
}
