<?php

return [
    'tri-campus-provider' => true,

    /*
     * Configure Shibboleth OIDC authentication.
     */
    'providers' => [
        'uis' => [
            'config_url' => env('UIS_OIDC_CONFIG_URL', null),
            'client_id' => env('UIS_OIDC_CLIENT_ID', null),
            'client_secret' => env('UIS_OIDC_SECRET_ID', ''),
            'auth_url' => env('UIS_OIDC_AUTH_URL', null),
            'token_url' => env('UIS_OIDC_TOKEN_URL', null),
            'user_url' => env('UIS_OIDC_USER_URL', null),
            'logout_url' => env('UIS_OIDC_LOGOUT_URL', null),
            'redirect' => env('APP_URL').'/auth/callback',
            'user-mapping' => [
                'uin' => 'uisedu_uin',
                'full_name' => 'full_name',
                'first_name' => 'given_name',
                'last_name' => 'family_name',
                'preferred_first_name' => 'preferred_first_name',
                'email' => 'email',
                'netid' => 'preferred_username',
                'groups' => 'uisedu_is_member_of',
            ],
            'introspect' => [
                'introspect_url' => env('UIS_INTROSPECT_URL', null),
                'client_id' => env('UIS_INTROSPECT_CLIENT_ID', null),
                'client_secret' => env('UIS_INTROSPECT_CLIENT_SECRET', null),
            ],
            'scopes' => ['openid', 'profile', 'email', 'address', 'phone', 'offline_access'],
        ],
        'uic' => [
            'config_url' => env('UIC_OIDC_CONFIG_URL', null),
            'client_id' => env('UIC_OIDC_CLIENT_ID', null),
            'client_secret' => env('UIC_OIDC_SECRET_ID', ''),
            'auth_url' => env('UIC_OIDC_AUTH_URL', null),
            'token_url' => env('UIC_OIDC_TOKEN_URL', null),
            'user_url' => env('UIC_OIDC_USER_URL', null),
            'logout_url' => env('UIC_OIDC_LOGOUT_URL', null),
            'redirect' => env('APP_URL').'/auth/callback',
            'user-mapping' => [
                'uin' => 'itrust_uin',
                'full_name' => 'name',
                'first_name' => 'given_name',
                'last_name' => 'family_name',
                'email' => 'email',
                'netid' => 'preferred_username',
                'groups' => 'is_member_of',
            ],
            'introspect' => [
                'introspect_url' => env('UIC_INTROSPECT_URL', null),
                'client_id' => env('UIC_INTROSPECT_CLIENT_ID', null),
                'client_secret' => env('UIC_INTROSPECT_CLIENT_SECRET', null),
            ],
            'scopes' => ['openid', 'profile', 'email'],
        ],
        'uiuc' => [
            'config_url' => env('UIUC_OIDC_CONFIG_URL', null),
            'client_id' => env('UIUC_OIDC_CLIENT_ID', null),
            'client_secret' => env('UIUC_OIDC_SECRET_ID', ''),
            'auth_url' => env('UIUC_OIDC_AUTH_URL', null),
            'token_url' => env('UIUC_OIDC_TOKEN_URL', null),
            'user_url' => env('UIUC_OIDC_USER_URL', null),
            'logout_url' => env('UIUC_OIDC_LOGOUT_URL', null),
            'redirect' => env('APP_URL').'/auth/callback',
            'user-mapping' => [
                'uin' => 'itrust_uin',
                'full_name' => 'full_name',
                'first_name' => 'given_name',
                'last_name' => 'family_name',
                'email' => 'email',
                'netid' => 'preferred_username',
                'groups' => 'uiucedu_is_member_of',
            ],
            'introspect' => [
                'introspect_url' => env('UIUC_INTROSPECT_URL', null),
                'client_id' => env('UIUC_INTROSPECT_CLIENT_ID', null),
                'client_secret' => env('UIUC_INTROSPECT_CLIENT_SECRET', null),
            ],
            'scopes' => ['openid', 'profile', 'email'],
        ],
    ],

];
