<?php

namespace UisIts\Oidc\Saml;

use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Two\User;

abstract class AbstractSamlProvider
{
    protected ?User $user;

    /**
     * Attributes set in $_SERVER after successful authentication
     */
    protected array $attributes;

    /**
     * Builds the url to redirect for authentication
     */
    abstract public function getAuthUrl(): string;

    /**
     * Redirect the user to IDP to authenticate
     */
    abstract public function redirect(): RedirectResponse;

    /**
     * Return a Socialite User object for the authenticated user
     */
    abstract public function user(): User;

    /**
     * Map the array of user attributes to Socialite User object
     */
    abstract public function mapUserToObject(array $user): User;
}
