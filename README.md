# Laravel OIDC

Laravel OIDC is a comprehensive authentication package for Laravel applications that provides seamless integration with OpenID Connect (OIDC) authentication. Built for the University of Illinois system, it supports authentication across three campuses — UIS, UIC, and UIUC — with a unified tri-campus selection flow, token introspection for API security, and role-based authorization via the Spatie Permissions package.

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
  - [Environment Variables](#environment-variables)
  - [Config File Reference](#config-file-reference)
- [Authentication Flows](#authentication-flows)
  - [Single-Campus Authentication](#single-campus-authentication)
  - [Tri-Campus Authentication](#tri-campus-authentication)
  - [Logout](#logout)
- [Token Introspection](#token-introspection)
- [Roles and Permissions](#roles-and-permissions)
- [Database](#database)
- [Published Assets](#published-assets)
- [Routes](#routes)
- [Exceptions](#exceptions)
- [Testing](#testing)
- [Code Style](#code-style)
- [Issues and Security](#issues-and-security)

---

## Requirements

- PHP `^8.4`
- Laravel `^10.0 | ^11.0 | ^12.0 | ^13.0`

---

## Installation

### 1. Install the package via Composer

```bash
composer require uisits/laravel-oidc
```

### 2. Run the install command

```bash
php artisan shibboleth:install
```

This command performs the following actions automatically:

- Installs `spatie/laravel-permission` and publishes its config and migrations.
- Publishes the package config file to `config/shibboleth-oidc.php`.
- Publishes migrations to `database/migrations/`.
- Publishes CSS and image assets to `public/`.

### 3. Set environment variables

Populate your `.env` file with the required OIDC credentials for each campus you intend to support. See [Environment Variables](#environment-variables) for the full list.

### 4. Run migrations

```bash
php artisan migrate
```

This adds the following columns to your `users` table: `netid`, `uin`, `first_name`, `last_name`, `preferred_first_name`, `access_token`, `id_token`, and `refresh_token`.

---

## Configuration

After running the install command, a config file is published to `config/shibboleth-oidc.php`. Below is a full reference of all available options.

### Config File Reference

```php
return [

    /*
    |--------------------------------------------------------------------------
    | Tri-Campus Provider
    |--------------------------------------------------------------------------
    | Set to true to display a campus selection screen at login, allowing
    | users from UIS, UIC, or UIUC to authenticate with their respective
    | identity provider.
    |
    */
    'tri-campus-provider' => env('TRI_CAMPUS_PROVIDER', true),

    'providers' => [

        'uis' => [
            'config_url'    => env('UIS_OIDC_CONFIG_URL'),
            'client_id'     => env('UIS_OIDC_CLIENT_ID'),
            'client_secret' => env('UIS_OIDC_SECRET_ID'),
            'auth_url'      => env('UIS_OIDC_AUTH_URL'),
            'token_url'     => env('UIS_OIDC_TOKEN_URL'),
            'user_url'      => env('UIS_OIDC_USER_URL'),
            'logout_url'    => env('UIS_OIDC_LOGOUT_URL'),
            'redirect'      => env('APP_URL') . '/auth/callback',
            'scopes'        => ['openid', 'profile', 'email'],

            'user-mapping' => [
                'uin'                  => 'uisedu_uin',
                'full_name'            => 'full_name',
                'first_name'           => 'given_name',
                'last_name'            => 'family_name',
                'preferred_first_name' => 'preferred_first_name',
                'email'                => 'email',
                'netid'                => 'preferred_username',
                'groups'               => 'uisedu_is_member_of',
            ],

            'introspect' => [
                'url'           => env('UIS_INTROSPECT_URL'),
                'client_id'     => env('UIS_INTROSPECT_CLIENT_ID'),
                'client_secret' => env('UIS_INTROSPECT_CLIENT_SECRET'),
            ],
        ],

        'uic' => [
            'config_url'    => env('UIC_OIDC_CONFIG_URL'),
            'client_id'     => env('UIC_OIDC_CLIENT_ID'),
            'client_secret' => env('UIC_OIDC_SECRET_ID'),
            'auth_url'      => env('UIC_OIDC_AUTH_URL'),
            'token_url'     => env('UIC_OIDC_TOKEN_URL'),
            'user_url'      => env('UIC_OIDC_USER_URL'),
            'logout_url'    => env('UIC_OIDC_LOGOUT_URL'),
            'redirect'      => env('APP_URL') . '/auth/callback',
            'scopes'        => ['openid', 'profile', 'email'],

            'user-mapping' => [
                'uin'        => 'itrust_uin',
                'full_name'  => 'name',
                'first_name' => 'given_name',
                'last_name'  => 'family_name',
                'email'      => 'email',
                'netid'      => 'preferred_username',
                'groups'     => 'is_member_of',
            ],

            'introspect' => [
                'url'           => env('UIC_INTROSPECT_URL'),
                'client_id'     => env('UIC_INTROSPECT_CLIENT_ID'),
                'client_secret' => env('UIC_INTROSPECT_CLIENT_SECRET'),
            ],
        ],

        'uiuc' => [
            'config_url'    => env('UIUC_OIDC_CONFIG_URL'),
            'client_id'     => env('UIUC_OIDC_CLIENT_ID'),
            'client_secret' => env('UIUC_OIDC_SECRET_ID'),
            'auth_url'      => env('UIUC_OIDC_AUTH_URL'),
            'token_url'     => env('UIUC_OIDC_TOKEN_URL'),
            'user_url'      => env('UIUC_OIDC_USER_URL'),
            'logout_url'    => env('UIUC_OIDC_LOGOUT_URL'),
            'redirect'      => env('APP_URL') . '/auth/callback',
            'scopes'        => ['openid', 'profile', 'email'],

            'user-mapping' => [
                'uin'        => 'itrust_uin',
                'full_name'  => 'full_name',
                'first_name' => 'given_name',
                'last_name'  => 'family_name',
                'email'      => 'email',
                'netid'      => 'preferred_username',
                'groups'     => 'uiucedu_is_member_of',
            ],

            'introspect' => [
                'url'           => env('UIUC_INTROSPECT_URL'),
                'client_id'     => env('UIUC_INTROSPECT_CLIENT_ID'),
                'client_secret' => env('UIUC_INTROSPECT_CLIENT_SECRET'),
            ],
        ],
    ],
];
```

### Environment Variables

Add the following to your `.env` file. Only populate the sections for campuses your application supports.

```dotenv
# ─── UIS ──────────────────────────────────────────────────────────────────────
UIS_OIDC_CONFIG_URL=
UIS_OIDC_CLIENT_ID=
UIS_OIDC_SECRET_ID=
UIS_OIDC_AUTH_URL=
UIS_OIDC_TOKEN_URL=
UIS_OIDC_USER_URL=
UIS_OIDC_LOGOUT_URL=
UIS_INTROSPECT_URL=
UIS_INTROSPECT_CLIENT_ID=
UIS_INTROSPECT_CLIENT_SECRET=

# ─── UIC ──────────────────────────────────────────────────────────────────────
UIC_OIDC_CONFIG_URL=
UIC_OIDC_CLIENT_ID=
UIC_OIDC_SECRET_ID=
UIC_OIDC_AUTH_URL=
UIC_OIDC_TOKEN_URL=
UIC_OIDC_USER_URL=
UIC_OIDC_LOGOUT_URL=
UIC_INTROSPECT_URL=
UIC_INTROSPECT_CLIENT_ID=
UIC_INTROSPECT_CLIENT_SECRET=

# ─── UIUC ─────────────────────────────────────────────────────────────────────
UIUC_OIDC_CONFIG_URL=
UIUC_OIDC_CLIENT_ID=
UIUC_OIDC_SECRET_ID=
UIUC_OIDC_AUTH_URL=
UIUC_OIDC_TOKEN_URL=
UIUC_OIDC_USER_URL=
UIUC_OIDC_LOGOUT_URL=
UIUC_INTROSPECT_URL=
UIUC_INTROSPECT_CLIENT_ID=
UIUC_INTROSPECT_CLIENT_SECRET=

# ─── General ──────────────────────────────────────────────────────────────────
TRI_CAMPUS_PROVIDER=true
APP_URL=https://your-app.example.com
```

---

## Authentication Flows

### Single-Campus Authentication

To lock your application to a single campus, disable the tri-campus provider and the package will always route users to the UIS identity provider.

In `config/shibboleth-oidc.php`:

```php
'tri-campus-provider' => false,
```

**Flow:**

1. User visits `/login`.
2. The package immediately redirects to the UIS OIDC authorization endpoint.
3. After authentication, the identity provider redirects back to `/auth/callback`.
4. The package exchanges the authorization code for tokens (using PKCE).
5. User attributes are fetched from the userinfo endpoint and mapped to your `users` table.
6. The user is created or updated, then logged into the Laravel session.
7. User is redirected to `/`.

### Tri-Campus Authentication

When `tri-campus-provider` is `true` (the default), users are presented with a campus selection screen before being redirected to their identity provider.

**Flow:**

1. User visits `/login`.
2. The package renders the campus selection view (`tri-campus.blade.php`).
3. User selects their campus (UIS, UIC, or UIUC) and submits.
4. The selected campus is stored in the session.
5. User is redirected to the appropriate campus OIDC authorization endpoint.
6. After authentication, the callback, token exchange, and user upsert proceed identically to the single-campus flow.
7. User is redirected to `/`.

**User attributes stored on login:**

| Column | Source Claim (UIS) | Description |
|---|---|---|
| `uin` | `uisedu_uin` | 9-digit University Identification Number |
| `name` | `full_name` | Full display name |
| `first_name` | `given_name` | Legal first name |
| `last_name` | `family_name` | Legal last name |
| `preferred_first_name` | `preferred_first_name` | Preferred/chosen first name |
| `email` | `email` | Primary email address (lowercased) |
| `netid` | `preferred_username` | Campus NetID username (lowercased) |
| `access_token` | — | OAuth 2.0 access token |
| `id_token` | — | OpenID Connect ID token |
| `refresh_token` | — | OAuth 2.0 refresh token |

### Logout

Visiting `/logout` will:

1. Retrieve the user's campus from the session.
2. Invalidate the Laravel session and log the user out locally.
3. Redirect to the identity provider's logout endpoint to end the SSO session.

---

## Token Introspection

The `Introspect` middleware validates OAuth 2.0 bearer tokens on API routes by calling the campus identity provider's token introspection endpoint.

### Setup

Register the middleware alias in `bootstrap/app.php` (Laravel 11+):

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'introspect' => \UisIts\Oidc\Http\Middleware\Introspect::class,
    ]);
})
```

For Laravel 10, add it to the `$middlewareAliases` array in `app/Http/Kernel.php`:

```php
protected $middlewareAliases = [
    // ...
    'introspect' => \UisIts\Oidc\Http\Middleware\Introspect::class,
];
```

### Basic Usage

Apply the middleware to any route that requires a valid token:

```php
Route::middleware(['introspect'])->get('/api/profile', function (Request $request) {
    return response()->json(['user' => $request->user()]);
});
```

### Scope Validation

Pass required scopes as middleware arguments to enforce that the token has specific permissions:

```php
Route::middleware(['introspect:openid,profile'])->get('/api/data', function () {
    // Only accessible with tokens that include 'openid' and 'profile' scopes
});
```

### Retrieving the User from a Token

Use the static helper to resolve a user from a bearer token directly:

```php
use UisIts\Oidc\Http\Middleware\Introspect;

Route::middleware(['introspect'])->get('/api/me', function (Request $request) {
    $user = Introspect::getUserFromToken($request->bearerToken());

    return response()->json($user);
});
```

**Example response:**

```php
[
    "sub"                    => "xyz@abc.org",
    "uisedu_is_member_of"    => [ /* array of group memberships */ ],
    "uisedu_uin"             => "123456789",
    "preferred_username"     => "xyz",
    "given_name"             => "John",
    "preferred_display_name" => "Doe, John",
    "family_name"            => "Doe",
    "email"                  => "xyz@abc.org",
]
```

### HTTP Response Codes

| Condition | Status |
|---|---|
| No `Authorization` header present | `403 Forbidden` |
| Token is missing, expired, or inactive | `401 Unauthorized` |
| Token is missing required scopes | `401 Unauthorized` |
| Token is valid | Passes to next middleware |

---

## Roles and Permissions

The install command sets up [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission) automatically. After running `php artisan migrate`, you can assign roles and permissions to authenticated users.

**Assign a role:**

```php
$user->assignRole('editor');
```

**Check a permission:**

```php
if ($user->can('publish articles')) {
    // ...
}
```

**Create roles and permissions** using the Spatie package directly or in database seeders:

```php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

Role::create(['name' => 'admin']);
Permission::create(['name' => 'manage users']);

Role::findByName('admin')->givePermissionTo('manage users');
```

Refer to the [Spatie documentation](https://spatie.be/docs/laravel-permission/v6/basic-usage/basic-usage) for advanced usage.

---

## Database

The package migration adds the following columns to your existing `users` table:

| Column | Type | Constraints | Description |
|---|---|---|---|
| `netid` | `string(20)` | unique | Campus NetID username |
| `uin` | `string(9)` | unique | 9-digit University Identification Number |
| `first_name` | `string(100)` | — | Legal first name |
| `last_name` | `string(100)` | — | Legal last name |
| `preferred_first_name` | `string(100)` | nullable | Chosen/preferred first name |
| `access_token` | `longText` | nullable | OAuth 2.0 access token |
| `id_token` | `longText` | nullable | OIDC ID token |
| `refresh_token` | `longText` | nullable | OAuth 2.0 refresh token |

> **Note:** Tokens are stored in plaintext. Ensure your database is appropriately secured and restrict access to these columns as needed.

---

## Published Assets

The install command publishes the following assets. You can also publish them individually using the tags below.

| Tag | Source | Destination |
|---|---|---|
| `shibboleth-config` | `config/shibboleth-oidc.php` | `config/shibboleth-oidc.php` |
| `shibboleth-migrations` | `migrations/` | `database/migrations/` |
| `shibboleth-assets` | `dist/shibboleth-oidc.css` | `public/css/shibboleth-oidc.css` |
| `shibboleth-assets` | `resources/images/illinois-system-logo.svg` | `public/images/illinois-system-logo.svg` |
| `shibboleth-views` | `resources/views/` | `resources/views/vendor/laravel-oidc/` |

To publish a specific group manually:

```bash
php artisan vendor:publish --tag=shibboleth-config
php artisan vendor:publish --tag=shibboleth-migrations
php artisan vendor:publish --tag=shibboleth-assets
php artisan vendor:publish --tag=shibboleth-views
```

To customize the campus selection UI, publish the views and edit the Blade template at `resources/views/vendor/laravel-oidc/tri-campus.blade.php`.

---

## Routes

The following routes are registered automatically by the package:

| Name | Method | URI | Description |
|---|---|---|---|
| `login` | `GET` | `/login` | Initiates the OIDC login flow or shows the campus selection screen |
| `callback` | `GET` | `/auth/callback` | OIDC redirect callback; exchanges code for tokens and logs user in |
| `logout` | `GET` | `/logout` | Logs out the user locally and redirects to the IdP logout endpoint |
| `tri-campus-discovery.show` | `GET` | `/tri-campus-discovery` | Displays the campus selection form |
| `tri-campus-discovery.update` | `POST` | `/tri-campus-discovery` | Handles campus form submission and redirects to the chosen IdP |

---

## Exceptions

The package throws the following exceptions. You can catch these in your application's exception handler (`app/Exceptions/Handler.php`) to customize error responses.

| Exception | Thrown When |
|---|---|
| `UisIts\Oidc\Exceptions\AuthenticationException` | User is not authenticated when required |
| `UisIts\Oidc\Exceptions\DriverMissingConfigurationException` | A required config key is absent for the selected provider |
| `UisIts\Oidc\Exceptions\InvalidaCampusSelectionException` | An invalid campus value is submitted |
| `UisIts\Oidc\Exceptions\InvalidStateException` | The OAuth state parameter does not match (CSRF protection) |
| `UisIts\Oidc\Exceptions\InvalidLogoutException` | The logout redirect could not be resolved |
| `UisIts\Oidc\Exceptions\RequestFailedException` | An HTTP request to the identity provider failed |
| `UisIts\Oidc\Exceptions\ValueNotFoundException` | A required value is missing from the OIDC response |

**Example — handle a missing campus config gracefully:**

```php
use UisIts\Oidc\Exceptions\DriverMissingConfigurationException;

public function register(): void
{
    $this->renderable(function (DriverMissingConfigurationException $e, Request $request) {
        return response()->view('errors.oidc-misconfigured', [], 500);
    });
}
```

---

## Testing

Run the package test suite with Pest:

```bash
./vendor/bin/pest
```

---

## Code Style

Fix code style automatically using Laravel Pint:

```bash
./vendor/bin/pint
```

---

## Issues and Security

For bugs or feature requests, please [open an issue](https://github.com/uisits/laravel-oidc/issues) on GitHub with a detailed description and any relevant logs.

For security vulnerabilities, **do not** open a public issue. Instead, email the UIS ITS ADDS Team directly at [uisappdevdl@uis.edu](mailto:uisappdevdl@uis.edu).
