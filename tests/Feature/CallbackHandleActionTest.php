<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use UisIts\Oidc\Contracts\Factory;

uses(RefreshDatabase::class);

test('throws when campus is not in session', function () {
    $response = $this->get('/auth/callback?code=test-code');

    $response->assertStatus(500);
});

test('creates user in database on successful uis callback', function () {
    app(Factory::class)->extend('uis', function ($app) {
        return (new \Tests\Stubs\UisProviderStub(
            $app->make('request'),
            'test-uis-client-id',
            'test-uis-secret',
            'http://localhost/auth/callback'
        ))->stateless();
    });

    $this->withSession(['oidc.campus' => 'uis'])
         ->get('/auth/callback?code=test-code')
         ->assertRedirect('/');

    $this->assertDatabaseHas('users', [
        'uin' => '123456789',
        'netid' => 'jdoe',
        'email' => 'jdoe@uis.edu',
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);
});

test('logs user in after successful callback', function () {
    app(Factory::class)->extend('uis', function ($app) {
        return (new \Tests\Stubs\UisProviderStub(
            $app->make('request'),
            'test-uis-client-id',
            'test-uis-secret',
            'http://localhost/auth/callback'
        ))->stateless();
    });

    $this->withSession(['oidc.campus' => 'uis'])
         ->get('/auth/callback?code=test-code');

    $this->assertAuthenticated();
});

test('updates existing user on subsequent callback', function () {
    app(Factory::class)->extend('uis', function ($app) {
        return (new \Tests\Stubs\UisProviderStub(
            $app->make('request'),
            'test-uis-client-id',
            'test-uis-secret',
            'http://localhost/auth/callback'
        ))->stateless();
    });

    $this->withSession(['oidc.campus' => 'uis'])->get('/auth/callback?code=first-code');
    $this->withSession(['oidc.campus' => 'uis'])->get('/auth/callback?code=second-code');

    $this->assertDatabaseCount('users', 1);
});
