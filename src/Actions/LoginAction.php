<?php

namespace UisIts\Oidc\Actions;

use UisIts\Oidc\Facades\Oidc;

class LoginAction
{
    public function __invoke()
    {
        if(config('shibboleth-oidc.tri-campus-provider')) {
            Oidc::driver('tri-campus')->redirect();
        }

        return Oidc::driver('uis-oidc')->redirect();
    }
}