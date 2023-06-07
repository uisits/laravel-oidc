<?php

use Illuminate\Contracts\Session\Session;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Two\User;
use PrasadChinwal\Shibboleth\Test\stubs\OidcProviderStub;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

beforeEach(function () {
    $this->codeVerifier = null;
});

test('can generate proper url for shib oidc driver', function () {
    $request = Request::create('foo');
    $request->setLaravelSession($session = Mockery::mock(Session::class));

    $state = null;
    $sessionPutClosure = function ($name, $value) use (&$state) {
        if ($name === 'state') {
            $state = $value;

            return true;
        } elseif ($name === 'code_verifier') {
            $this->codeVerifier = $value;

            return true;
        }

        return false;
    };

    $sessionPullClosure = function ($name) {
        if ($name === 'code_verifier') {
            return $this->codeVerifier;
        }
    };

    $session->expects('put')->twice()->withArgs($sessionPutClosure);
    $session->expects('get')->with('code_verifier')->andReturnUsing($sessionPullClosure);

    $provider = new OidcProviderStub($request, 'client_id', 'client_secret', 'redirect');
    $response = $provider->redirect();

    $codeChallenge = rtrim(strtr(base64_encode(hash('sha256', $this->codeVerifier, true)), '+/', '-_'), '=');

    $this->assertInstanceOf(SymfonyRedirectResponse::class, $response);
    $this->assertInstanceOf(RedirectResponse::class, $response);
    $this->assertSame('http://auth.url?client_id=client_id&redirect_uri=redirect&scope=openid+profile+email+phone+address+offline_access&response_type=code&state='.$state.'&code_challenge='.$codeChallenge.'&code_challenge_method=S256', $response->getTargetUrl());
});

test('can set appropriate scopes for oidc authentication', function () {
    $request = Request::create('foo');
    $request->setLaravelSession($session = Mockery::mock(Session::class));

    $state = null;
    $sessionPutClosure = function ($name, $value) use (&$state) {
        if ($name === 'state') {
            $state = $value;

            return true;
        } elseif ($name === 'code_verifier') {
            $this->codeVerifier = $value;

            return true;
        }

        return false;
    };

    $sessionPullClosure = function ($name) {
        if ($name === 'code_verifier') {
            return $this->codeVerifier;
        }
    };
    config()->set(['shibboleth.oidc.scopes' => ['openid', 'profile']]);
    $session->expects('put')->twice()->withArgs($sessionPutClosure);
    $session->expects('get')->with('code_verifier')->andReturnUsing($sessionPullClosure);

    $provider = new OidcProviderStub($request, 'client_id', 'client_secret', 'redirect');
    $response = $provider->redirect();
    $this->assertInstanceOf(SymfonyRedirectResponse::class, $response);
    $this->assertInstanceOf(RedirectResponse::class, $response);
    expect(config('shibboleth.oidc.scopes'))->toEqual($provider->getScopes());
});

test('successful authentication returns an instance of User', function () {
    $user = loginUser();
    $this->assertInstanceOf(User::class, $user);
    $this->assertSame('123456789', $user->uin);
    $this->assertSame('first last', $user->first_name.' '.$user->last_name);
    $this->assertSame($user->name, $user->first_name.' '.$user->last_name);
    $this->assertSame('abc', $user->netid);
    $this->assertSame('abc@xxx.org', $user->netid.'@xxx.org');
    $this->assertSame('access_token', $user->token);
    $this->assertSame('refresh_token', $user->refreshToken);
    $this->assertSame(3600, $user->expiresIn);
    $this->assertSame($user->uin, $user->user['uisedu_uin']);
});

test('authenticated user has admin role if member of group', function () {
    $user = loginUser();
    expect(config('shibboleth.oidc.authorization'))->toBeIn($user->groups);
});

/**
 * Login a test user
 */
function loginUser(): User
{
    $request = Request::create('foo', 'GET', ['state' => str_repeat('A', 40), 'code' => 'code']);
    $request->setLaravelSession($session = Mockery::mock(Session::class));
    $codeVerifier = Str::random(32);
    $session->expects('pull')->with('state')->andReturns(str_repeat('A', 40));
    $session->expects('pull')->with('code_verifier')->andReturns($codeVerifier);
    $provider = new OidcProviderStub($request, 'client_id', 'client_secret', 'redirect_uri');
    $provider->http = Mockery::mock(stdClass::class);
    $provider->http->expects('post')->with('http://token.url', [
        'headers' => ['Accept' => 'application/json'], 'form_params' => ['grant_type' => 'authorization_code', 'client_id' => 'client_id', 'client_secret' => 'client_secret', 'code' => 'code', 'redirect_uri' => 'redirect_uri', 'code_verifier' => $codeVerifier],
    ])->andReturns($response = Mockery::mock(stdClass::class));
    $response->expects('getBody')->andReturns('{ "access_token" : "access_token", "refresh_token" : "refresh_token", "expires_in" : 3600 }');

    return $provider->user();
}
