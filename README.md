# Laravel Shibboleth

This package extends the Laravel's first-party package socialite to authenticate and authorize using Shibboleth.

## Usage:
- Install the package:
```composer require prasadchinwal/laravel-shibboleth```
- Optional: Add Service provider to `config/app.php` file.
```prasadchinwal/shibboleth/ShibbolethServiceProvider::class```
- Install the package:
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
use PrasadChinwal\Shibboleth\Actions\AuthHandler;
Route::name('login')->get('login', [AuthHandler::class, 'login']);
Route::name('callback')->get('/auth/callback', [AuthHandler::class, 'callback']);
Route::name('logout')->get('/logout', [AuthHandler::class, 'logout']);
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
'introspect' => \PrasadChinwal\Shibboleth\Http\Middleware\Introspect::class,
```

Now you can use the middleware on your protected route as such:
```php
Route::middleware(['introspect'])->get('/introspect', 'Controller@index')
->name('introspect');
```
Note: Getting the user's netid from token
 
You can get the user's netid associated with token by:
```php
Session::get('introspect.username');
```

#### Code Style
You can use Laravel pint to automatically fix code styles.
```./vendor/bin/pint```

## Testing
You can run the tests for the package using pest.
``` ./vendor/bin/pest```

## Issues and Concerns
Please open an issue on the GitHub repository with detailed description and logs (if available).
> In case of security concerns please write an email to [PrasadChinwal](prasadchinwal5@gmail.com). 
