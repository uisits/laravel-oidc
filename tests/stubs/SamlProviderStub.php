<?php

namespace PrasadChinwal\Shibboleth\Test\stubs;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Two\User;
use Mockery as m;
use PrasadChinwal\Shibboleth\Saml\AbstractSamlProvider;
use stdClass;

class SamlProviderStub extends AbstractSamlProvider
{
    public function getAuthUrl(): string
    {
        return \str('https://')
            ->append('auth.url')
            ->append(':')
            ->append('1001')
            ->append('/app_url')
            ->append('?target=')
            ->append('/redirect_url')
            ->value();
    }

    public function redirect(): RedirectResponse
    {
        return new RedirectResponse($this->getAuthUrl());
    }

    public function user(): User
    {
        $this->attributes = [
            'iTrustUIN' => '123456789',
            'givenName' => 'first',
            'sn' => 'last',
            'cn' => 'abc',
            'name' => 'first last',
            'mail' => 'abc@xxx.org',
        ];

        return $this->mapUserToObject($this->attributes);
    }

    public function mapUserToObject(array $user): User
    {
        return (new User)->setRaw($user)->map([
            'uin' => $user['iTrustUIN'],
            'name' => $user['givenName'].' '.$user['sn'],
            'first_name' => $user['givenName'],
            'last_name' => $user['sn'],
            'email' => $user['mail'],
            'netid' => $user['cn'],
            'password' => Hash::make($user['iTrustUIN'].now()),
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
}
