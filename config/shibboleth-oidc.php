<?php

return [
    'tri-campus-provider' => false,

    /*
     * Configure Shibboleth OIDC authentication.
     */
    'providers' => [
        'uis' => [
            'config_url' => env('UIS_OIDC_CONFIG_URL'),
            'client_id' => env('UIS_OIDC_CLIENT_ID'),
            'client_secret' => '',
            'auth_url' => env('UIS_OIDC_AUTH_URL'),
            'token_url' => env('UIS_OIDC_TOKEN_URL'),
            'user_url' => env('UIS_OIDC_USER_URL'),
            'logout_url' => env('UIS_OIDC_LOGOUT_URL'),
            'redirect' => env('APP_URL').'/auth/callback',
            'user-mapping' => [
                'uin' => 'uisedu_uin',
                'full_name' => 'full_name',
                'first_name' => 'given_name',
                'last_name' => 'family_name',
                'email' => 'email',
                'netid' => 'preferred_username',
            ],
            'introspect' => [
                'introspect_url' => env('UIS_INTROSPECT_URL'),
                'client_id' => env('UIS_INTROSPECT_CLIENT_ID', null),
                'client_secret' => env('UIS_INTROSPECT_CLIENT_SECRET', null),
            ],
            'scopes' => ['openid', 'profile', 'email', 'address', 'phone', 'offline_access']
        ],
        'chicago' => [

        ],
        'urbana' => [],
    ],

];
