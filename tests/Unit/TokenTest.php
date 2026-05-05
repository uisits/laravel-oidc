<?php

use UisIts\Oidc\Token;

test('token stores all properties on construction', function () {
    $token = new Token(
        token: 'access-token-abc',
        refreshToken: 'refresh-token-xyz',
        expiresIn: 3600,
        approvedScopes: ['openid', 'profile', 'email'],
    );

    expect($token->token)->toBe('access-token-abc')
        ->and($token->refreshToken)->toBe('refresh-token-xyz')
        ->and($token->expiresIn)->toBe(3600)
        ->and($token->approvedScopes)->toBe(['openid', 'profile', 'email']);
});

test('token accepts empty approved scopes', function () {
    $token = new Token('tok', 'ref', 1800, []);

    expect($token->approvedScopes)->toBeArray()->toBeEmpty();
});

test('token accepts multiple approved scopes', function () {
    $scopes = ['openid', 'profile', 'email', 'phone', 'address'];
    $token = new Token('tok', 'ref', 900, $scopes);

    expect($token->approvedScopes)->toHaveCount(5)->toBe($scopes);
});
