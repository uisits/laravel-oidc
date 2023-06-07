<?php

use Laravel\Socialite\Contracts\Factory;
use UisIts\Oidc\Oidc\ShibbolethOidcProvider;
use UisIts\Oidc\Saml\ShibbolethSamlProvider;

test('can instantiate oidc provider', function () {
    $factory = app()->make(Factory::class);
    $socialiteDriver = $factory->driver('shib-oidc');
    expect($socialiteDriver)->toBeInstanceOf(ShibbolethOidcProvider::class);
});

test('can instantiate saml provider', function () {
    $factory = app()->make(Factory::class);
    $socialiteDriver = $factory->driver('shib-saml');
    expect($socialiteDriver)->toBeInstanceOf(ShibbolethSamlProvider::class);
});
