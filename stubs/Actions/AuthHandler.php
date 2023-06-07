<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthHandler
{
    /**
     * Handle authenticated User
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login()
    {
        $user = Socialite::driver(config('shibboleth.type'))->user();
        $user = User::updateOrCreate([
            'uin' => $user->uin,
        ], [
            'uin' => $user->uin,
            'name' => $user->name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'netid' => $user->netid,
            'email' => $user->email,
            'token' => $user->token,
            'remember_token' => $user->refreshToken,
            'password' => $user->password,
        ]);
        Auth::login($user);

        return redirect('/');
    }

    /**
     * Logout Currently authenticated User
     */
    public function logout()
    {
        return Socialite::driver(config('shibboleth.type'))->logout();
    }
}
