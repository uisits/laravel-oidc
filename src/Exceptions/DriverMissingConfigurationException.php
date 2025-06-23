<?php

namespace UisIts\Oidc\Exceptions;

use InvalidArgumentException;

class DriverMissingConfigurationException extends InvalidArgumentException
{
    /**
     * Create a new exception for a missing configuration.
     *
     * @param string $provider
     * @param array<int, string> $keys
     * @return static
     */
    public static function make(string $provider, array $keys): static
    {
        /** @phpstan-ignore new.static */
        return new static('Missing required configuration keys ['.implode(', ', $keys)."] for [{$provider}] OAuth provider.");
    }
}