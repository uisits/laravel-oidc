<?php

namespace UisIts\Oidc\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class AuthController
{
    /**
     * Redirect login to oidc provider
     */
    public function login(): \Illuminate\Http\RedirectResponse
    {
        return Socialite::driver(config('shibboleth.type'))->redirect();
    }

    /**
     * Handle authenticated User
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback()
    {
        $socialiteUser = Socialite::driver(config('shibboleth.type'))->user();
        $user = User::updateOrCreate([
            'uin' => $socialiteUser->uin,
        ], [
            'uin' => $socialiteUser->uin,
            'name' => $socialiteUser->name,
            'first_name' => $socialiteUser->first_name,
            'last_name' => $socialiteUser->last_name,
            'netid' => $socialiteUser->netid,
            'email' => $socialiteUser->email,
            'token' => $socialiteUser->token,
            'remember_token' => $socialiteUser->refreshToken,
            'password' => $socialiteUser->password,
        ]);

        $role = Role::firstOrCreate(['name' => 'admin']);

        if (in_array(config('shibboleth.authorization'), $socialiteUser->groups)) {
            $user->assignRole($role);
        }

        Auth::login($user);

        return redirect()->to($this->getRedirectUrl());
    }

    /**
     * Logout Currently authenticated User
     */
    public function logout()
    {
        return Socialite::driver(config('shibboleth.type'))->logout();
    }

    /**
     * Get redirect url after successful authentication.
     */
    protected function getRedirectUrl(): string
    {
        if (empty(config('shibboleth.redirect_to'))) {
            return '/';
        }

        return config('shibboleth.redirect_to');
    }
}
