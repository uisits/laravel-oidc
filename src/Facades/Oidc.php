<?php

namespace UisIts\Oidc\Facades;

use Illuminate\Support\Facades\Facade;
use UisIts\Oidc\Contracts\Factory;
use UisIts\Oidc\Contracts\Provider;
use UisIts\Oidc\Providers\AbstractOidcProvider;
use UisIts\Oidc\ShibbolethManager;

/**
 * @method static Provider driver(string $driver = null)
 * @method static AbstractOidcProvider buildProvider(string $provider, array $config)
 * @method static ShibbolethManager extend(string $driver, \Closure $callback)
 * @method array getScopes()
 * @method Provider scopes(array|string $scopes)
 * @method Provider setScopes(array|string $scopes)
 * @method Provider redirectUrl(string $url)
 *
 * @see \Laravel\Socialite\SocialiteManager
 */
class Oidc extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return Factory::class;
    }
}