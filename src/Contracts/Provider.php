<?php

namespace UisIts\Oidc\Contracts;

use Illuminate\Support\Collection;

interface Provider
{
    /**
     * Redirect the user to the authentication page for the provider.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Illuminate\Http\RedirectResponse
     */
    public function redirect();

    /**
     * Get the User instance for the authenticated user.
     *
     * @return \UisIts\Oidc\User
     */
    public function user(): \UisIts\Oidc\User;
}
