<?php

use UisIts\Oidc\Exceptions\DriverMissingConfigurationException;

test('make creates instance with correct message format', function () {
    $exception = DriverMissingConfigurationException::make(
        'MyProvider',
        ['redirect', 'client_id']
    );

    expect($exception->getMessage())
        ->toBe('Missing required configuration keys [redirect, client_id] for [MyProvider] OAuth provider.');
});

test('exception is an instance of invalid argument exception', function () {
    $exception = DriverMissingConfigurationException::make('Provider', ['key']);

    expect($exception)->toBeInstanceOf(InvalidArgumentException::class);
});

test('make includes all provided missing keys in message', function () {
    $exception = DriverMissingConfigurationException::make(
        'UisProvider',
        ['client_id', 'client_secret', 'redirect']
    );

    expect($exception->getMessage())
        ->toContain('client_id')
        ->toContain('client_secret')
        ->toContain('redirect')
        ->toContain('UisProvider');
});

test('make with single key formats message without extra comma', function () {
    $exception = DriverMissingConfigurationException::make('Provider', ['redirect']);

    expect($exception->getMessage())
        ->toBe('Missing required configuration keys [redirect] for [Provider] OAuth provider.');
});
