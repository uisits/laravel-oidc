# Laravel Shibboleth

Laravel Shibboleth is a comprehensive authentication package for Laravel applications that provides seamless integration with Shibboleth and OpenID Connect (OIDC) authentication protocols.
This package supports OIDC authentication methods, allowing flexible implementation based on your organization's requirements.
It includes features for user authorization via Spatie/permissions package, token introspection, and a simple installation process to get your authentication system up and running quickly.



## Usage:
- Install the package:
```composer require uisits/laravel-oidc```


- **Important:** Install the package:
``` php artisan shibboleth:install```

> Running this command performs the following actions:
> - Installs `spatie/laravel-permission` in your app.
> - Publish assets such as images, build assets to appropriate directories in your project.
> - Publish the `shibboleth-oidc.php` config file to your config folder.
> - Publish migrations.

- Set environment variables in .env file (Check the `config/shibboleth.php` file)
- For Tri-Campus authentication, set the environment variables as per the config file and set `'tri-campus-provider' => true,` in `config/shibboleth-oidc.php` file.

#### Migrate database
Run `php artisan migrate`

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

#### Code Style
You can use Laravel pint to automatically fix code styles.
```./vendor/bin/pint```

## Testing
You can run the tests for the package using pest.
``` ./vendor/bin/pest```

## Issues and Concerns
Please open an issue on the GitHub repository with detailed description and logs (if available).
> In case of security concerns, please write an email to [UIS ITS ADDS Team](uisappdevdl@uis.edu). 
