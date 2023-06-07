<?php

namespace PrasadChinwal\Shibboleth\Test;

use Closure;
use Orchestra\Testbench\TestCase as Orchestra;
use PrasadChinwal\Shibboleth\ShibbolethServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @return string[]
     */
    protected function getPackageProviders($app): array
    {
        return [
            ShibbolethServiceProvider::class,
        ];
    }

    /**
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('shibboleth.type', 'oidc');

        // Oidc config
        $app['config']->set('shibboleth.oidc.client_id', 'github-client-id');
        $app['config']->set('shibboleth.oidc.client_secret', 'client-id');
        $app['config']->set('shibboleth.oidc.auth_url', 'auth-url');
        $app['config']->set('shibboleth.oidc.token_url', 'token-url');
        $app['config']->set('shibboleth.oidc.logout_url', 'logout-url');
        $app['config']->set('shibboleth.oidc.redirect', 'redirect');
        $app['config']->set('shibboleth.oidc.authorization', 'test-group');
        $app['config']->set('shibboleth.oidc.scopes', ['openid', 'profile', 'email', 'phone', 'address', 'offline_access']);

        // Saml Config
        $app['config']->set('shibboleth.saml.auth_url', 'auth-url');
        $app['config']->set('shibboleth.saml.logout_url', 'logout-url');
        $app['config']->set('shibboleth.saml.redirect', 'redirect');
        $app['config']->set('shibboleth.saml.entitlement', 'isMemberOf');
        $app['config']->set('shibboleth.saml.user', ['sn', 'givenName', 'name', 'mail', 'iTrustUIN']);

        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function setUpDatabaseRequirements(Closure $callback): void
    {
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../migrations');
    }
}
