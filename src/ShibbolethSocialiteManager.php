<?php

namespace UisIts\Oidc;

use Laravel\Socialite\SocialiteManager;
use Laravel\Socialite\Two\AbstractProvider;
use UisIts\Oidc\Oidc\ShibbolethOidcProvider;
use UisIts\Oidc\Saml\ShibbolethSamlProvider;

class ShibbolethSocialiteManager extends SocialiteManager
{
    /**
     * Create a shibboleth oidc driver
     */
    public function createShibOidcDriver(): AbstractProvider
    {
        $config = $this->config->get('shibboleth.oidc');

        return $this->buildProvider(ShibbolethOidcProvider::class, $config);
    }

    /**
     * Create a shibboleth saml driver
     */
    public function createShibSamlDriver(): ShibbolethSamlProvider
    {
        return new ShibbolethSamlProvider;
    }
}
