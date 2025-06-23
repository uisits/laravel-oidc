<?php

namespace UisIts\Oidc\Contracts;

interface Factory
{
    /**
     * Get an OAuth provider implementation.
     *
     * @param string|null $driver
     */
    public function driver(string $driver = null);
}