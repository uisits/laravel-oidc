<?php

namespace UisIts\Oidc\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use UisIts\Oidc\Enums\Campus;
use UisIts\Oidc\Facades\Oidc;

class CallbackHandleAction
{
    public function __invoke()
    {
        if (Session::missing('oidc.campus')) {
            throw new \InvalidArgumentException('Campus not set');
        }

        $user = match (Session::get('oidc.campus')) {
            Campus::UIS->value => Oidc::driver('uis')->user(),
            Campus::UIC->value => Oidc::driver('uic')->user(),
            Campus::UIUC->value => Oidc::driver('uiuc')->user(),
            default => throw new \InvalidArgumentException('Campus not set!'),
        };

        $userClass = config('auth.providers.users.model');

        $user = $userClass::updateOrCreate([
            'uin' => $user->uin,
        ], [
            'uin' => $user->uin,
            'name' => $user->name,
            'first_name' => $user->firstName,
            'last_name' => $user->lastName,
            'preferred_first_name' => $user->preferred_first_name ?? '',
            'netid' => Str::lower($user->netid),
            'email' => Str::lower($user->email),
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
            '/' : config('shibboleth-oidc.providers.uis.redirect');
    }
}
