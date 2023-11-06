# Laravel Shibboleth

This package extends the Laravel's first-party package socialite to authenticate and authorize using Shibboleth.

## Usage:
- Install the package:
```composer require uisits/laravel-oidc```
- Optional: Add Service provider to `config/app.php` file.
```UisIts/Oidc/ShibbolethServiceProvider::class```
- **Important:** Install the package:
``` php artisan shibboleth:install```
- Set environment variables in .env file (Check the `config/shibboleth.php` file)

#### Migrate database
Run `php artisan migrate`

> Note:
> 
> For Authorization set `APP_AD_AUTHORIZE_GROUP` in the .env file.
> 
> You can check user is admin using gates or directly using user model. ex:
> 
> ```php
> In AuthServiceProvider:
> Gate::define('admin', function (User $user) {
>    return $user->hasRole('admin');
> });
> 
> To check if user is admin you can either use:
> User::find()->hasRole
> 
> OR
> 
> Gate::allows('admin')
> ```

#### Using SAML authentication 
- Set the SAML environment variables
- Set the type property in `config/shibboleth.php` to ***saml***

#### Using OIDC authentication
- Set the OIDC environment variables
- Set the type property in `config/shibboleth.php` to ***oidc***

#### Set up authentication routes
set the authentication routes in `routes/web.php` files
```php
use UisIts\Oidc\Http\Controllers\AuthController;

Route::name('login')->get('login', [AuthController::class, 'login']);

Route::name('callback')->get('/auth/callback', [AuthController::class, 'callback']);

Route::name('logout')->get('/logout', [AuthController::class, 'logout']);
```

#### Authorization
- Define the ad group name in the .env file
- You can configure the redirect route to use after successfully authentication by overriding the `redirect_to` property in the `config/shibboleth.php` file. 
- Set up the name of the group in `config/shibboleth.php` file under the `authorization` property
  `'authorization' => env('APP_AD_AUTHORIZE_GROUP', null)`
- Add the trait `HasRoles` to the `Users` model
    ```php
    use Spatie\Permission\Traits\HasRoles;
    class User extends Authenticatable
    {
        use HasRoles;
    }
    ```
- In your `app/AuthServiceProvider.php` file you can now assign Gates or check if user is admin anywhere in the application using the below logic:
  ```php
    # In AuthServiceProvider
    Gate::define('admin', function (User $user) {
        return $user->hasRole('admin');
    });
    # OR
    $user->hasRole('admin');
  ```

You can extend the roles and permissions functionality to add new roles or permissions using [Spatie Permission package](https://spatie.be/docs/laravel-permission/v5/basic-usage/basic-usage)

#### Token Introspection
For token introspection using OIDC add the following middleware to the `app/Http/Kernel.php` file:

Under `alias` property:
```php
'introspect' => \UisIts\Oidc\Http\Middleware\Introspect::class,
```

Now you can use the middleware on your protected route as such:
```php
use UisIts\Oidc\Http\Middleware\Introspect;

Route::middleware(['introspect'])->get('/introspect', function (Request $request) {
    dump($request->bearerToken());
    dd(Introspect::getUserFromToken($request->bearerToken()));
})->name('introspect');
```
Note: Below is the response received when you get a user from token
```php
Introspect::getUserFromToken($request->bearerToken());

array:8 [▼ // routes/api.php:24
  "sub" => "xyz@abc.org"
  "uisedu_is_member_of" => array:42 [▶]
  "uisedu_uin" => "123456789"
  "preferred_username" => "xyz"
  "given_name" => "John"
  "preferred_display_name" => "Doe, John"
  "family_name" => "Doe"
  "email" => "xyz@abc.org"
];
```

#### Logging
To help with easier logging this package sets a custom header `REMOTE_SERVER`
using the middleware `\UisIts\Oidc\Http\Middleware\AddOidcMiddleware` class.
It adds the `users netid` to `$_SERVER['REMOTE_USER']` and `$request->headers`.

You can enable this by adding the `\UisIts\Oidc\Http\Middleware\AddOidcMiddleware::class`
in the `app/Http/Kernel.php` file under the `$middleware` property.
For example:
```php
protected $middleware = [
    // \App\Http\Middleware\TrustHosts::class,
    \App\Http\Middleware\TrustProxies::class,
    \Illuminate\Http\Middleware\HandleCors::class,
    \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
    \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
    \App\Http\Middleware\TrimStrings::class,
    \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    \UisIts\Oidc\Http\Middleware\AddOidcHeader::class,
];
```
> Note:
> 
> To configure this behaviour you can create your own middleware using
`php artisan make:middleware` and then extend your middleware with `AddOidcHeader`.
Now you can override the `handle` method with your own implementation.

#### Code Style
You can use Laravel pint to automatically fix code styles.
```./vendor/bin/pint```

## Testing
You can run the tests for the package using pest.
``` ./vendor/bin/pest```

## Issues and Concerns
Please open an issue on the GitHub repository with detailed description and logs (if available).
> In case of security concerns please write an email to [UIS ITS ADDS Team](uisappdevdl@uis.edu). 
