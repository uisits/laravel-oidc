<?php

namespace Tests\Stubs;

use UisIts\Oidc\Providers\UisProvider;

class UisProviderStub extends UisProvider
{
    public function getAccessTokenResponse($code): array
    {
        return [
            'access_token' => 'fake-access-token',
            'refresh_token' => 'fake-refresh-token',
            'id_token' => 'fake-id-token',
            'expires_in' => 3600,
            'scope' => 'openid profile email',
        ];
    }

    public function getUserByToken($token): array
    {
        return [
            'uisedu_uin' => '123456789',
            'preferred_username' => 'jdoe',
            'given_name' => 'John',
            'family_name' => 'Doe',
            'preferred_first_name' => 'Johnny',
            'email' => 'jdoe@uis.edu',
            'uisedu_is_member_of' => ['test-group'],
        ];
    }
}
