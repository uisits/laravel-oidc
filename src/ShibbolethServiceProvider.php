<?php

namespace UisIts\Oidc;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use UisIts\Oidc\Console\ShibbolethInstall;
use UisIts\Oidc\Contracts\Factory;
use UisIts\Oidc\Oidc\ShibbolethOidcProvider;

class ShibbolethServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/shibboleth-oidc.php', 'shibboleth-oidc');

        $this->loadRoutesFrom(realpath(__DIR__.'/routes/routes.php'));

        $this->loadMigrationsFrom(__DIR__.'/../migrations');

        $this->loadViewsFrom(__DIR__.'/resources/views', 'laravel-oidc');

        $this->publishes([
            __DIR__.'/../migrations' => database_path('migrations'),
        ], 'shibboleth-migrations');

        $this->publishes([
            __DIR__.'/../config/shibboleth-oidc.php' => config_path('shibboleth-oidc.php'),
        ], 'shibboleth-config');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Factory::class, function ($app) {
            return new ShibbolethManager($app);
        });

        // Register the shibboleth:install command
        if ($this->app->runningInConsole()) {
            $this->commands(ShibbolethInstall::class);
        }
    }
}
