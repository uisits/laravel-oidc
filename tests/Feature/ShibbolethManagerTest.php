<?php

use UisIts\Oidc\Contracts\Factory;
use UisIts\Oidc\Exceptions\DriverMissingConfigurationException;
use UisIts\Oidc\Facades\Oidc;
use UisIts\Oidc\Providers\UicProvider;
use UisIts\Oidc\Providers\UisProvider;
use UisIts\Oidc\Providers\UiucProvider;
use UisIts\Oidc\ShibbolethManager;

test('factory contract resolves to shibboleth manager', function () {
    expect(app(Factory::class))->toBeInstanceOf(ShibbolethManager::class);
});

test('uis driver resolves to UisProvider', function () {
    expect(Oidc::driver('uis'))->toBeInstanceOf(UisProvider::class);
});

test('uic driver resolves to UicProvider', function () {
    expect(Oidc::driver('uic'))->toBeInstanceOf(UicProvider::class);
});

test('uiuc driver resolves to UiucProvider', function () {
    expect(Oidc::driver('uiuc'))->toBeInstanceOf(UiucProvider::class);
});

test('getDefaultDriver throws InvalidArgumentException', function () {
    expect(fn () => app(Factory::class)->getDefaultDriver())
        ->toThrow(InvalidArgumentException::class, 'No driver was specified.');
});

test('buildProvider throws when all config keys are missing', function () {
    expect(fn () => app(Factory::class)->buildProvider(UisProvider::class, []))
        ->toThrow(DriverMissingConfigurationException::class);
});

test('buildProvider throws listing the specific missing keys', function () {
    expect(fn () => app(Factory::class)->buildProvider(UisProvider::class, ['client_id' => 'id']))
        ->toThrow(DriverMissingConfigurationException::class, 'client_secret');
});

test('buildProvider with requires only redirect client_id client_secret', function () {
    $provider = app(Factory::class)->buildProvider(UisProvider::class, [
        'client_id' => 'id',
        'client_secret' => 'secret',
        'redirect' => 'http://localhost/callback',
    ]);

    expect($provider)->toBeInstanceOf(UisProvider::class);
});
