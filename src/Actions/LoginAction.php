<?php

namespace UisIts\Oidc\Actions;

use Illuminate\Support\Facades\Session;
use UisIts\Oidc\Enums\Campus;
use UisIts\Oidc\Facades\Oidc;

class LoginAction
{
    public function __invoke()
    {
        if (config('shibboleth-oidc.tri-campus-provider')) {
            return view('laravel-oidc::tri-campus');
        }

        Session::put('oidc.campus', Campus::UIS->value);

        return Oidc::driver('uis')->redirect();
    }
}
