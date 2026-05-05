<?php

use UisIts\Oidc\User;

test('user setters return the same instance for chaining', function () {
    $user = new User;

    expect($user->setToken('tok'))->toBe($user)
        ->and($user->setIdToken('id-tok'))->toBe($user)
        ->and($user->setRefreshToken('ref'))->toBe($user)
        ->and($user->setExpiresIn(3600))->toBe($user)
        ->and($user->setApprovedScopes(['openid']))->toBe($user);
});

test('user token properties are set and readable', function () {
    $user = (new User)
        ->setToken('access')
        ->setIdToken('id-token')
        ->setRefreshToken('refresh')
        ->setExpiresIn(7200)
        ->setApprovedScopes(['openid', 'profile']);

    expect($user->token)->toBe('access')
        ->and($user->idToken)->toBe('id-token')
        ->and($user->refreshToken)->toBe('refresh')
        ->and($user->expiresIn)->toBe(7200)
        ->and($user->approvedScopes)->toBe(['openid', 'profile']);
});

test('setRaw stores raw data and getRaw returns it', function () {
    $raw = ['sub' => 'user-123', 'email' => 'test@uis.edu', 'given_name' => 'Jane'];
    $user = (new User)->setRaw($raw);

    expect($user->getRaw())->toBe($raw);
});

test('map populates known properties', function () {
    $user = (new User)->map([
        'name' => 'John Doe',
        'email' => 'john@uis.edu',
        'uin' => '123456789',
        'netid' => 'jdoe',
        'firstName' => 'John',
        'lastName' => 'Doe',
    ]);

    expect($user->name)->toBe('John Doe')
        ->and($user->email)->toBe('john@uis.edu')
        ->and($user->uin)->toBe('123456789')
        ->and($user->netid)->toBe('jdoe')
        ->and($user->firstName)->toBe('John')
        ->and($user->lastName)->toBe('Doe');
});

test('map stores unknown keys in attributes and accessible via __get', function () {
    $user = (new User)->map(['preferred_first_name' => 'Johnny', 'groups' => ['admin']]);

    expect($user->preferred_first_name)->toBe('Johnny')
        ->and($user->groups)->toBe(['admin']);
});

test('__get returns null for nonexistent attribute', function () {
    $user = new User;

    expect($user->doesNotExist)->toBeNull();
});

test('getName and getEmail return mapped values', function () {
    $user = (new User)->map(['name' => 'Jane Doe', 'email' => 'jane@uic.edu']);

    expect($user->getName())->toBe('Jane Doe')
        ->and($user->getEmail())->toBe('jane@uic.edu');
});

test('user implements array access on raw data', function () {
    $user = (new User)->setRaw(['sub' => 'u-1', 'email' => 'test@uiuc.edu']);

    expect(isset($user['sub']))->toBeTrue()
        ->and(isset($user['missing']))->toBeFalse()
        ->and($user['email'])->toBe('test@uiuc.edu');
});

test('user array access allows setting and unsetting', function () {
    $user = (new User)->setRaw([]);

    $user['key'] = 'value';
    expect($user['key'])->toBe('value');

    unset($user['key']);
    expect(isset($user['key']))->toBeFalse();
});
