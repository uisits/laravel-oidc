<?php

namespace UisIts\Oidc\Saml;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

final class ShibbolethSamlProvider extends AbstractSamlProvider implements ProviderInterface
{
    public function getAuthUrl(): string
    {
        return \str('https://')
            ->append(Request::server('SERVER_NAME'))
            ->append(':')
            ->append(Request::server('SERVER_PORT'))
            ->append(config('shibboleth.saml.auth_url'))
            ->append('?target=')
            ->append(config('shibboleth.saml.redirect'))
            ->value();
    }

    public function redirect(): RedirectResponse
    {
        return new RedirectResponse($this->getAuthUrl());
    }

    /**
     * Return a Socialite User object for the authenticated user
     */
    public function user(): User
    {
        $this->attributes = Arr::only($_SERVER, config('shibboleth.saml.user'));

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
            'groups' => $user['isMemberOf'],
        ]);
    }

    /**
     * Logout currently authenticated User
     */
    public function logout(): RedirectResponse
    {
        Auth::logout();
        Session::flush();

        return new RedirectResponse(config('shibboleth.oidc.logout_url'));
    }
}
