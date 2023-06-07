<?php

return [
    /*
     * Configure authentication and authorization type.
     *
     * Supported: shib-saml, shib-oidc
     */
    'type' => 'shib-oidc',

    /*
     * Configure route to redirect to after authentication.
     */
    'redirect_to' => '/',

    /*
     * Configure Shibboleth OIDC authentication.
     */
    'oidc' => [
        'client_id' => env('OIDC_CLIENT_ID'),
        'client_secret' => '',
        'auth_url' => env('OIDC_AUTH_URL'),
        'token_url' => env('OIDC_TOKEN_URL'),
        'user_url' => env('OIDC_USER_URL'),
        'logout_url' => env('OIDC_LOGOUT_URL'),
        'redirect' => env('APP_URL').'/auth/callback',
        'scopes' => ['openid', 'profile', 'email', 'phone', 'address', 'offline_access'],
    ],

    /*
     * Configure token introspection params.
     */
    'introspect' => [
        'introspect_url' => env('INTROSPECT_URL'),
        'client_id' => env('INTROSPECT_CLIENT_ID', null),
        'client_secret' => env('INTROSPECT_CLIENT_SECRET', null),
    ],

    /*
     * Configure Shibboleth OIDC authentication.
     */
    'saml' => [
        'auth_url' => env('SAML_LOGIN_URL'),
        'logout_url' => env('SAML_LOGOUT_URL'),
        'redirect' => env('APP_URL').'/auth/callback',
        'entitlement' => 'isMemberOf',
        'user' => ['sn', 'givenName', 'name', 'mail', 'iTrustUIN'],
    ],

    /*
     * Configure the authorization AD group
     */
    'authorization' => env('APP_AD_AUTHORIZE_GROUP', null),

    /*
     * Configure to set user attributes to $_SERVER global variable.
     */
    'mapping' => [
        'maps_user_attributes' => true,
        'attributes' => ['netid', 'uin', 'name', 'first_name', 'last_name', 'email', 'groups'],
    ],

];
