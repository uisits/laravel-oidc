<?php

namespace UisIts\Oidc\Contracts;

interface Factory
{
    /**
     * Get an OAuth provider implementation.
     */
    public function driver(string $driver = null);
}
