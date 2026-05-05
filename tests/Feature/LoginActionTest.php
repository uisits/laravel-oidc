<?php

test('redirects to uis auth url when tri-campus is disabled', function () {
    config(['shibboleth-oidc.tri-campus-provider' => false]);

    $response = $this->get('/login');

    $response->assertRedirect();
    expect($response->headers->get('Location'))
        ->toContain('https://uis.test/oidc/authorize');
});

test('stores uis campus in session when tri-campus is disabled', function () {
    config(['shibboleth-oidc.tri-campus-provider' => false]);

    $this->get('/login');

    expect(session('oidc.campus'))->toBe('uis');
});

test('shows tri-campus discovery view when tri-campus is enabled', function () {
    config(['shibboleth-oidc.tri-campus-provider' => true]);

    $response = $this->get('/login');

    $response->assertOk();
    $response->assertSee('Choose a campus to login');
});

test('tri-campus view contains campus options', function () {
    config(['shibboleth-oidc.tri-campus-provider' => true]);

    $response = $this->get('/login');

    $response->assertSee('University of Illinois Springfield')
        ->assertSee('University of Illinois Chicago')
        ->assertSee('University of Illinois Urbana-Champaign');
});
