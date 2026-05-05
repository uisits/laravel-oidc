<?php

use UisIts\Oidc\Exceptions\ValueNotFoundException;
use UisIts\Oidc\Facades\Oidc;

test('uis provider returns configured scopes', function () {
    $scopes = Oidc::driver('uis')->getScopes();

    expect($scopes)
        ->toContain('openid')
        ->toContain('profile')
        ->toContain('email')
        ->toContain('address')
        ->toContain('phone')
        ->toContain('offline_access');
});

test('uis provider getScopes throws when scopes not configured', function () {
    config(['shibboleth-oidc.providers.uis.scopes' => null]);

    expect(fn () => Oidc::driver('uis')->getScopes())
        ->toThrow(ValueNotFoundException::class, 'Scopes not set in config file');
});

test('uis provider redirect returns redirect response to auth url', function () {
    $response = $this->get('/login');

    $response->assertRedirect();
    expect($response->headers->get('Location'))
        ->toContain('https://uis.test/oidc/authorize');
});

test('uis provider redirect includes pkce code challenge', function () {
    $response = $this->get('/login');

    expect($response->headers->get('Location'))
        ->toContain('code_challenge=')
        ->toContain('code_challenge_method=S256');
});

test('uis provider redirect includes client id and redirect uri', function () {
    $response = $this->get('/login');

    $location = $response->headers->get('Location');
    expect($location)
        ->toContain('client_id=test-uis-client-id')
        ->toContain('redirect_uri=')
        ->toContain('response_type=code');
});

test('uis provider redirect stores pkce verifier in session', function () {
    $this->get('/login');

    expect(session()->has('code_verifier'))->toBeTrue();
});

test('uis provider scopes separator is a space', function () {
    $response = $this->get('/login');

    $location = $response->headers->get('Location');
    parse_str(parse_url($location, PHP_URL_QUERY), $params);

    expect($params['scope'])->toContain(' ');
});

test('uic provider returns configured scopes', function () {
    $scopes = Oidc::driver('uic')->getScopes();

    expect($scopes)->toContain('openid')->toContain('profile')->toContain('email');
});

test('uiuc provider returns configured scopes', function () {
    $scopes = Oidc::driver('uiuc')->getScopes();

    expect($scopes)->toContain('openid')->toContain('profile')->toContain('email');
});
