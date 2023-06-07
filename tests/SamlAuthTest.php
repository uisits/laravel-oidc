<?php

use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Two\User;
use PrasadChinwal\Shibboleth\Test\stubs\SamlProviderStub;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

beforeEach(function () {

});

test('can generate proper url for shib saml driver', function () {
    $provider = new SamlProviderStub();
    $response = $provider->redirect();

    $this->assertInstanceOf(SymfonyRedirectResponse::class, $response);
    $this->assertInstanceOf(RedirectResponse::class, $response);
    $this->assertSame('https://auth.url:1001/app_url?target=/redirect_url', $response->getTargetUrl());
});

test('successful authentication returns an instance of User', function () {
    $provider = new SamlProviderStub();
    $provider->http = Mockery::mock(stdClass::class);
    $response = $provider->redirect();

    $this->assertInstanceOf(SymfonyRedirectResponse::class, $response);
    $this->assertInstanceOf(RedirectResponse::class, $response);

    $user = $provider->user();
    $this->assertInstanceOf(User::class, $user);
    $this->assertSame('123456789', $user->uin);
    $this->assertSame('first last', $user->first_name.' '.$user->last_name);
    $this->assertSame($user->name, $user->first_name.' '.$user->last_name);
    $this->assertSame('abc', $user->netid);
    $this->assertSame('abc@xxx.org', $user->netid.'@xxx.org');
    $this->assertSame($user->uin, $provider->user()->uin);
});
