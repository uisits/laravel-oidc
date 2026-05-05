<?php

use UisIts\Oidc\Http\Middleware\Introspect;

test('checkScopes passes when all required scopes are present', function () {
    $middleware = new Introspect;

    $middleware->checkScopes('openid profile email', ['openid', 'email']);

    expect(true)->toBeTrue();
});

test('checkScopes passes when token has more scopes than required', function () {
    $middleware = new Introspect;

    $middleware->checkScopes('openid profile email phone address offline_access', ['openid', 'profile']);

    expect(true)->toBeTrue();
});

test('checkScopes passes with single required scope', function () {
    $middleware = new Introspect;

    $middleware->checkScopes('openid profile email', ['openid']);

    expect(true)->toBeTrue();
});

test('checkScopes throws when a required scope is missing', function () {
    $middleware = new Introspect;

    expect(fn () => $middleware->checkScopes('openid profile', ['openid', 'admin']))
        ->toThrow(InvalidArgumentException::class, 'Missing scopes admin');
});

test('checkScopes throws and lists all missing scopes', function () {
    $middleware = new Introspect;

    expect(fn () => $middleware->checkScopes('openid', ['openid', 'profile', 'email']))
        ->toThrow(InvalidArgumentException::class, 'Missing scopes');
});

test('checkScopes accepts scopes as a string', function () {
    $middleware = new Introspect;

    $middleware->checkScopes('openid profile email', 'openid');

    expect(true)->toBeTrue();
});
