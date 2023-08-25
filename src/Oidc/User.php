<?php

namespace UisIts\Oidc\Oidc;

use Laravel\Socialite\AbstractUser;

class User extends AbstractUser
{
    /**
     * The user's access token.
     *
     * @var string
     */
    public $token;

    /**
     * The refresh token that can be exchanged for a new access token.
     *
     * @var string
     */
    public $refreshToken;

    /**
     * The id token provided.
     */
    public $idToken;

    /**
     * The number of seconds the access token is valid for.
     *
     * @var int
     */
    public $expiresIn;

    /**
     * The scopes the user authorized. The approved scopes may be a subset of the requested scopes.
     *
     * @var array
     */
    public $approvedScopes;

    /**
     * Set the token on the user.
     *
     * @return $this
     */
    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Set the id token on the user.
     *
     * @return $this
     */
    public function setIdToken(string $idToken): static
    {
        $this->idToken = $idToken;

        return $this;
    }

    /**
     * Set the refresh token required to obtain a new access token.
     *
     * @return $this
     */
    public function setRefreshToken(string $refreshToken): static
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    /**
     * Set the number of seconds the access token is valid for.
     *
     * @param  int  $expiresIn
     * @return $this
     */
    public function setExpiresIn($expiresIn): static
    {
        $this->expiresIn = $expiresIn;

        return $this;
    }

    /**
     * Set the scopes that were approved by the user during authentication.
     *
     * @return $this
     */
    public function setApprovedScopes(array $approvedScopes): static
    {
        $this->approvedScopes = $approvedScopes;

        return $this;
    }
}
