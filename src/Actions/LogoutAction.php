<?php

namespace UisIts\Oidc\Actions;

use Illuminate\Support\Facades\Session;
use UisIts\Oidc\Enums\Campus;
use UisIts\Oidc\Facades\Oidc;

class LogoutAction
{
    public function __invoke()
    {
        if (Session::missing('oidc.campus')) {
            throw new \InvalidArgumentException('Campus not set');
        }

        return match (Session::get('oidc.campus')) {
            Campus::UIS->value => Oidc::driver('uis')->logout(),
            Campus::UIC->value => Oidc::driver('uic')->logout(),
            Campus::UIUC->value => Oidc::driver('uiuc')->logout(),
            default => throw new \InvalidArgumentException('Campus not set!'),
        };
    }
}
