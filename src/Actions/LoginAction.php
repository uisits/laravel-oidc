<?php

namespace UisIts\Oidc\Actions;

use UisIts\Oidc\Facades\Oidc;

class LoginAction
{
    public function __invoke()
    {
        if(config('shibboleth-oidc.tri-campus-provider')) {
            return view('laravel-oidc::tri-campus');
        }

        return Oidc::driver('uis')->redirect();
    }
}