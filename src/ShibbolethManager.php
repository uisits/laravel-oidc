<?php

namespace UisIts\Oidc;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Manager;
use Illuminate\Support\Str;
use InvalidArgumentException;
use UisIts\Oidc\Contracts\Factory;
use UisIts\Oidc\Exceptions\DriverMissingConfigurationException;
use UisIts\Oidc\Providers\AbstractOidcProvider;
use UisIts\Oidc\Providers\UicProvider;
use UisIts\Oidc\Providers\UisProvider;
use UisIts\Oidc\Providers\UiucProvider;

class ShibbolethManager extends Manager implements Factory
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     *
     * @deprecated Will be removed in a future Socialite release.
     */
    protected $app;

    /**
     * Get a driver instance.
     *
     * @return mixed
     */
    public function with(string $driver)
    {
        return $this->driver($driver);
    }

    /**
     * Build an OAuth 2 provider instance.
     *
     * @param  string  $provider
     * @param  array  $config
     * @return AbstractOidcProvider
     *
     * @throws BindingResolutionException
     */
    public function buildProvider($provider, $config)
    {
        $requiredKeys = ['client_id', 'client_secret', 'redirect'];

        $missingKeys = array_diff($requiredKeys, array_keys($config ?? []));

        if (! empty($missingKeys)) {
            throw DriverMissingConfigurationException::make($provider, $missingKeys);
        }

        return (new $provider(
            $this->container->make('request'),
            $config['client_id'],
            $config['client_secret'],
            $this->formatRedirectUrl($config),
            Arr::get($config, 'guzzle', [])
        ))->scopes($config['scopes'] ?? []);
    }

    /**
     * Format the server configuration.
     */
    public function formatConfig(array $config): array
    {
        return array_merge([
            'identifier' => $config['client_id'],
            'secret' => $config['client_secret'],
            'callback_uri' => $this->formatRedirectUrl($config),
        ], $config);
    }

    /**
     * Format the callback URL, resolving a relative URI if needed.
     */
    protected function formatRedirectUrl(array $config): string
    {
        $redirect = value($config['redirect']);

        return Str::startsWith($redirect ?? '', '/')
            ? $this->container->make('url')->to($redirect)
            : $redirect;
    }

    /**
     * Forget all of the resolved driver instances.
     *
     * @return $this
     */
    public function forgetDrivers()
    {
        $this->drivers = [];

        return $this;
    }

    /**
     * Set the container instance used by the manager.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return $this
     */
    public function setContainer($container)
    {
        $this->app = $container;
        $this->container = $container;
        $this->config = $container->make('config');

        return $this;
    }

    /**
     * Get the default driver name.
     *
     *
     * @throws InvalidArgumentException
     */
    public function getDefaultDriver(): string
    {
        throw new InvalidArgumentException('No driver was specified.');
    }

    /**
     * Create a driver for UIS campus.
     */
    public function createUisDriver(): AbstractOidcProvider
    {
        $config = $this->config->get('shibboleth-oidc.providers.uis');

        return $this->buildProvider(UisProvider::class, $config);
    }

    /**
     * Create a driver for UIC campus.
     */
    public function createUicDriver(): AbstractOidcProvider
    {
        $config = $this->config->get('shibboleth-oidc.providers.uic');

        return $this->buildProvider(UicProvider::class, $config);
    }

    /**
     * Create a driver for UIUC campus.
     */
    public function createUiucDriver(): AbstractOidcProvider
    {
        $config = $this->config->get('shibboleth-oidc.providers.uiuc');

        return $this->buildProvider(UiucProvider::class, $config);
    }
}
