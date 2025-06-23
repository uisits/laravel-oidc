<?php

namespace UisIts\Oidc\Actions;

use UisIts\Oidc\Facades\Oidc;

class LogoutAction
{
    public function __invoke()
    {
        return Oidc::driver('uis-oidc')->logout();
    }
}