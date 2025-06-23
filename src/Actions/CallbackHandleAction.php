<?php

namespace UisIts\Oidc\Actions;

use Illuminate\Support\Facades\Auth;
use UisIts\Oidc\Facades\Oidc;

class CallbackHandleAction
{
    public function __invoke()
    {
        $user = Oidc::driver('uis')->user();

        $userClass = config('auth.providers.users.model');

        $user = $userClass::updateOrCreate([
            'uin' => $user->uin,
        ], [
            'uin' => $user->uin,
            'name' => $user->name,
            'first_name' => $user->firstName,
            'last_name' => $user->lastName,
            'netid' => $user->netid,
            'email' => $user->email,
            'access_token' => $user->token,
            'id_token' => $user->idToken,
            'refresh_token' => $user->refreshToken,
            'password' => $user->password,
        ]);

        Auth::login($user);

        return redirect()->to($this->getRedirectUrl());
    }

    /**
     * Get redirect url after successful authentication.
     */
    protected function getRedirectUrl(): string
    {
        return empty(config('shibboleth-oidc.redirect_to')) ?
            '/': config('shibboleth-oidc.providers.uis.redirect');
    }
}