<?php

namespace Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use UisIts\Oidc\ShibbolethServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [ShibbolethServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        $app['config']->set('session.driver', 'array');
        $app['config']->set('auth.providers.users.model', \Tests\Stubs\User::class);
        $app['config']->set('shibboleth-oidc', [
            'tri-campus-provider' => false,
            'providers' => [
                'uis' => [
                    'client_id' => 'test-uis-client-id',
                    'client_secret' => 'test-uis-secret',
                    'auth_url' => 'https://uis.test/oidc/authorize',
                    'token_url' => 'https://uis.test/oidc/token',
                    'user_url' => 'https://uis.test/oidc/userinfo',
                    'logout_url' => 'https://uis.test/oidc/logout',
                    'redirect' => 'http://localhost/auth/callback',
                    'introspect_url' => 'https://uis.test/oidc/introspect',
                    'introspect' => [
                        'introspect_url' => 'https://uis.test/oidc/introspect',
                        'client_id' => 'introspect-client-id',
                        'client_secret' => 'introspect-secret',
                    ],
                    'user-mapping' => [
                        'uin' => 'uisedu_uin',
                        'full_name' => 'full_name',
                        'first_name' => 'given_name',
                        'last_name' => 'family_name',
                        'preferred_first_name' => 'preferred_first_name',
                        'email' => 'email',
                        'netid' => 'preferred_username',
                        'groups' => 'uisedu_is_member_of',
                    ],
                    'scopes' => ['openid', 'profile', 'email', 'address', 'phone', 'offline_access'],
                ],
                'uic' => [
                    'client_id' => 'test-uic-client-id',
                    'client_secret' => 'test-uic-secret',
                    'auth_url' => 'https://uic.test/oidc/authorize',
                    'token_url' => 'https://uic.test/oidc/token',
                    'user_url' => 'https://uic.test/oidc/userinfo',
                    'logout_url' => 'https://uic.test/oidc/logout',
                    'redirect' => 'http://localhost/auth/callback',
                    'introspect' => [
                        'introspect_url' => 'https://uic.test/oidc/introspect',
                        'client_id' => 'introspect-client-id',
                        'client_secret' => 'introspect-secret',
                    ],
                    'user-mapping' => [
                        'uin' => 'itrust_uin',
                        'full_name' => 'name',
                        'first_name' => 'given_name',
                        'last_name' => 'family_name',
                        'email' => 'email',
                        'netid' => 'preferred_username',
                        'groups' => 'is_member_of',
                    ],
                    'scopes' => ['openid', 'profile', 'email'],
                ],
                'uiuc' => [
                    'client_id' => 'test-uiuc-client-id',
                    'client_secret' => 'test-uiuc-secret',
                    'auth_url' => 'https://uiuc.test/oidc/authorize',
                    'token_url' => 'https://uiuc.test/oidc/token',
                    'user_url' => 'https://uiuc.test/oidc/userinfo',
                    'logout_url' => 'https://uiuc.test/oidc/logout',
                    'redirect' => 'http://localhost/auth/callback',
                    'introspect' => [
                        'introspect_url' => 'https://uiuc.test/oidc/introspect',
                        'client_id' => 'introspect-client-id',
                        'client_secret' => 'introspect-secret',
                    ],
                    'user-mapping' => [
                        'uin' => 'itrust_uin',
                        'full_name' => 'full_name',
                        'first_name' => 'given_name',
                        'last_name' => 'family_name',
                        'email' => 'email',
                        'netid' => 'preferred_username',
                        'groups' => 'uiucedu_is_member_of',
                    ],
                    'scopes' => ['openid', 'profile', 'email'],
                ],
            ],
        ]);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(\Orchestra\Testbench\default_migration_path());
    }
}
