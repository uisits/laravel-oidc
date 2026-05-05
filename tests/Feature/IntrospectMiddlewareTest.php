<?php

use Illuminate\Http\Request;
use UisIts\Oidc\Facades\Oidc;
use UisIts\Oidc\Http\Middleware\Introspect;

test('returns 403 when authorization header is missing', function () {
    $middleware = new Introspect;
    $request = Request::create('/test', 'GET');

    $response = $middleware->handle($request, fn ($r) => response('ok'));

    expect($response->getStatusCode())->toBe(403)
        ->and(json_decode($response->getContent(), true)['message'])
        ->toBe('Authorization Header not found!');
});

test('returns 401 when bearer token is empty', function () {
    $middleware = new Introspect;
    $request = Request::create('/test', 'GET');
    $request->headers->set('Authorization', 'Bearer ');

    $response = $middleware->handle($request, fn ($r) => response('ok'));

    expect($response->getStatusCode())->toBe(401)
        ->and(json_decode($response->getContent(), true)['message'])
        ->toBe('Token not set!');
});

test('throws when campus is not in session', function () {
    $middleware = new Introspect;
    $request = Request::create('/test', 'GET');
    $request->headers->set('Authorization', 'Bearer valid-token');

    expect(fn () => $middleware->handle($request, fn ($r) => response('ok')))
        ->toThrow(InvalidArgumentException::class, 'Campus not set');
});

test('returns 401 when introspected token is inactive', function () {
    $middleware = new Introspect;
    $request = Request::create('/test', 'GET');
    $request->headers->set('Authorization', 'Bearer inactive-token');

    $providerMock = Mockery::mock(\UisIts\Oidc\Providers\UisProvider::class);
    $providerMock->shouldReceive('introspect')
        ->with('inactive-token')
        ->andReturn(['active' => false]);

    Oidc::shouldReceive('driver')->with('uis')->andReturn($providerMock);

    $request->setLaravelSession(app('session.store'));
    $request->session()->put('oidc.campus', 'uis');

    $response = $middleware->handle($request, fn ($r) => response('ok'));

    expect($response->getStatusCode())->toBe(401)
        ->and(json_decode($response->getContent(), true)['message'])->toBe('Invalid Token!');
});

test('calls next middleware when token is active', function () {
    $middleware = new Introspect;
    $request = Request::create('/test', 'GET');
    $request->headers->set('Authorization', 'Bearer active-token');

    $providerMock = Mockery::mock(\UisIts\Oidc\Providers\UisProvider::class);
    $providerMock->shouldReceive('introspect')
        ->with('active-token')
        ->andReturn(['active' => true, 'scope' => 'openid profile']);

    Oidc::shouldReceive('driver')->with('uis')->andReturn($providerMock);

    $request->setLaravelSession(app('session.store'));
    $request->session()->put('oidc.campus', 'uis');

    $response = $middleware->handle($request, fn ($r) => response('passed', 200));

    expect($response->getStatusCode())->toBe(200)
        ->and($response->getContent())->toBe('passed');
});

test('throws when required scope is missing from active token', function () {
    $middleware = new Introspect;
    $request = Request::create('/test', 'GET');
    $request->headers->set('Authorization', 'Bearer scoped-token');

    $providerMock = Mockery::mock(\UisIts\Oidc\Providers\UisProvider::class);
    $providerMock->shouldReceive('introspect')
        ->with('scoped-token')
        ->andReturn(['active' => true, 'scope' => 'openid profile']);

    Oidc::shouldReceive('driver')->with('uis')->andReturn($providerMock);

    $request->setLaravelSession(app('session.store'));
    $request->session()->put('oidc.campus', 'uis');

    expect(fn () => $middleware->handle($request, fn ($r) => response('ok'), 'admin'))
        ->toThrow(InvalidArgumentException::class, 'Missing scopes admin');
});
