<picture>
  <source media="(prefers-color-scheme: dark)" srcset="https://banners.beyondco.de/Filament%20Socialite.png?theme=dark&packageManager=composer+require&packageName=DutchCodingCompany%2Ffilament-socialite&pattern=architect&style=style_1&description=Add+OAuth+login+through+Laravel+Socialite+to+Filament.&md=1&showWatermark=0&fontSize=100px&images=user-group">
  <img src="https://banners.beyondco.de/Filament%20Socialite.png?theme=light&packageManager=composer+require&packageName=DutchCodingCompany%2Ffilament-socialite&pattern=architect&style=style_1&description=Add+OAuth+login+through+Laravel+Socialite+to+Filament.&md=1&showWatermark=0&fontSize=100px&images=user-group">
</picture>

# Social login for Filament through Laravel Socialite

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dutchcodingcompany/filament-socialite.svg?style=flat-square)](https://packagist.org/packages/dutchcodingcompany/filament-socialite)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/dutchcodingcompany/filament-socialite/run-tests?label=tests)](https://github.com/dutchcodingcompany/filament-socialite/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/dutchcodingcompany/filament-socialite/Check%20&%20fix%20styling?label=code%20style)](https://github.com/dutchcodingcompany/filament-socialite/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/dutchcodingcompany/filament-socialite.svg?style=flat-square)](https://packagist.org/packages/dutchcodingcompany/filament-socialite)

Add OAuth2 login through Laravel Socialite to Filament. OAuth1 (eg. Twitter) is not supported at this time.

## Installation

| Filament version                                                                                                                                               | Package version | Readme                                                                               |
|----------------------------------------------------------------------------------------------------------------------------------------------------------------|-----------------|--------------------------------------------------------------------------------------|
| [^3.2.44](https://github.com/filamentphp/filament/releases/tag/v3.2.44) (if using [SPA mode](https://filamentphp.com/docs/3.x/panels/configuration#spa-mode))  | 2.x.x           | [Link](https://github.com/DutchCodingCompany/filament-socialite/blob/main/README.md) |
| [^3.2.44](https://github.com/filamentphp/filament/releases/tag/v3.2.44) (if using [SPA mode](https://filamentphp.com/docs/3.x/panels/configuration#spa-mode))  | ^1.3.1          |                                                                                      |
| 3.x                                                                                                                                                            | 1.x.x           | [Link](https://github.com/DutchCodingCompany/filament-socialite/blob/1.x/README.md)  |
| 2.x                                                                                                                                                            | 0.x.x           |                                                                                      |

Install the package via composer:

```bash
composer require dutchcodingcompany/filament-socialite
```

Publish and migrate the migration file:

```bash
php artisan vendor:publish --tag="filament-socialite-migrations"
php artisan migrate
```

Other configuration files include:
```bash
php artisan vendor:publish --tag="filament-socialite-config"
php artisan vendor:publish --tag="filament-socialite-views"
php artisan vendor:publish --tag="filament-socialite-translations"
```

You need to register the plugin in the Filament panel provider (the default filename is `app/Providers/Filament/AdminPanelProvider.php`). The following options are available:

```php
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Provider;
use Filament\Support\Colors;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Illuminate\Contracts\Auth\Authenticatable;

// ...
->plugin(
    FilamentSocialitePlugin::make()
        // (required) Add providers corresponding with providers in `config/services.php`. 
        ->providers([
            // Create a provider 'gitlab' corresponding to the Socialite driver with the same name.
            Provider::make('gitlab')
                ->label('GitLab')
                ->icon('fab-gitlab')
                ->color(Color::hex('#2f2a6b'))
                ->outlined(false)
                ->stateless(false)
                ->scopes(['...'])
                ->with(['...']),
        ])
        // (optional) Override the panel slug to be used in the oauth routes. Defaults to the panel ID.
        ->slug('admin')
        // (optional) Enable/disable registration of new (socialite-) users.
        ->registration(true)
        // (optional) Enable/disable registration of new (socialite-) users using a callback.
        // In this example, a login flow can only continue if there exists a user (Authenticatable) already.
        ->registration(fn (string $provider, SocialiteUserContract $oauthUser, ?Authenticatable $user) => (bool) $user)
        // (optional) Change the associated model class.
        ->userModelClass(\App\Models\User::class)
        // (optional) Change the associated socialite class (see below).
        ->socialiteUserModelClass(\App\Models\SocialiteUser::class)
);
```

This package automatically adds 2 routes per panel to make the OAuth flow possible: a redirector and a callback. When
setting up your **external OAuth app configuration**, enter the following callback URL (in this case for the Filament
panel with ID `admin` and the `github` provider):
```
https://example.com/admin/oauth/callback/github
```

A multi-panel callback route is available as well that does not contain the panel ID in the url. Instead, it determines
the panel ID from an encrypted `state` input (`...?state=abcd1234`). This allows you to create a single OAuth
application for multiple Filament panels that use the same callback URL. Note that this only works for _stateful_ OAuth
apps:

```
https://example.com/oauth/callback/github
```

If in doubt, run `php artisan route:list` to see which routes are available to you.

### CSRF protection
_(Laravel 11.x users can ignore this section)_

If your third-party provider calls the OAuth callback using a `POST` request, you need to add the callback route to the
exception list in your `VerifyCsrfToken` middleware. This can be done by adding the url to the `$except` array:

```php
protected $except = [
    '*/oauth/callback/*',
    'oauth/callback/*',
];
````

For Laravel 11.x users, this exception is automatically added by our service provider.

See [Socialite Providers](https://socialiteproviders.com/) for additional Socialite providers.

### Icons

You can specify a custom icon for each of your login providers. You can add Font Awesome brand
icons made available through [Blade Font Awesome](https://github.com/owenvoke/blade-fontawesome) by running:
```bash
composer require owenvoke/blade-fontawesome
```

### Registration flow

This package supports account creation for users. However, to support this flow it is important that the `password`
attribute on your `User` model is nullable. For example, by adding the following to your users table migration.
Or you could opt for customizing the user creation, see below.

```php
$table->string('password')->nullable();
```

### Domain Allow list

This package supports the option to limit the users that can login with the OAuth login to users of a certain domain.
This can be used to setup SSO for internal use.

```php
->plugin(
    FilamentSocialitePlugin::make()
        // ...
        ->registration(true)
        ->domainAllowList(['localhost'])
);
```

### Changing how an Authenticatable user is created or retrieved

You can use the `createUserUsing` and `resolveUserUsing` methods to change how a user is created or retrieved.

```php
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;

->plugin(
    FilamentSocialitePlugin::make()
        // ...
        ->createUserUsing(function (string $provider, SocialiteUserContract $oauthUser, FilamentSocialitePlugin $plugin) {
            // Logic to create a new user.
        })
        ->resolveUserUsing(function (string $provider, SocialiteUserContract $oauthUser, FilamentSocialitePlugin $plugin) {
            // Logic to retrieve an existing user.
        })
        ...
);
```

### Change how a Socialite user is created or retrieved

In your plugin options in your Filament panel, add the following method:

```php
// app/Providers/Filament/AdminPanelProvider.php
->plugins([
    FilamentSocialitePlugin::make()
        // ...
        ->socialiteUserModelClass(\App\Models\SocialiteUser::class)
```

This class should at the minimum implement the [`FilamentSocialiteUser`](/src/Models/Contracts/FilamentSocialiteUser.php) interface, like so:

```php
namespace App\Models;

use DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser as FilamentSocialiteUserContract;
use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;

class SocialiteUser implements FilamentSocialiteUserContract
{
    public function getUser(): Authenticatable
    {
        //
    }

    public static function findForProvider(string $provider, SocialiteUserContract $oauthUser): ?self
    {
        //
    }
    
    public static function createForProvider(
        string $provider,
        SocialiteUserContract $oauthUser,
        Authenticatable $user
    ): self {
        //
    }
}
```

### Check if the user is authorized to use the application

You can use the `authorizeUserUsing` method to check if the user is authorized to use the application. **Note:** by [default](/src/Traits/Callbacks.php#L145) this method check if the user's email domain is in the domain allow list.

```php
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;

->plugin(
    FilamentSocialitePlugin::make()
        // ...
        ->authorizeUserUsing(function (FilamentSocialitePlugin $plugin, SocialiteUserContract $oauthUser) {
            // Logic to authorize the user.
            return FilamentSocialitePlugin::checkDomainAllowList($plugin, $oauthUser);
        })
        // ...
);
```

### Change login redirect

When your panel has [multi-tenancy](https://filamentphp.com/docs/3.x/panels/tenancy) enabled, after logging in, the user will be redirected to their [default tenant](https://filamentphp.com/docs/3.x/panels/tenancy#setting-the-default-tenant).
If you want to change this behavior, you can call the 'redirectAfterLoginUsing' method on the `FilamentSocialitePlugin`.

```php
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser as FilamentSocialiteUserContract;
use DutchCodingCompany\FilamentSocialite\Models\SocialiteUser;

FilamentSocialitePlugin::make()
    ->redirectAfterLoginUsing(function (string $provider, FilamentSocialiteUserContract $socialiteUser, FilamentSocialitePlugin $plugin) {
        // Change the redirect behaviour here.
    });
```

### Filament Fortify

This component can also be added while using the [Fortify plugin](https://filamentphp.com/plugins/fortify) plugin.

```php
## in Service Provider file
public function boot()
{
    //...
    
    Filament::registerRenderHook(
        'filament-fortify.login.end',
        fn (): string => Blade::render('<x-filament-socialite::buttons />'),
    );
}
```

### Filament Breezy

This component can also be added while using the [Breezy plugin](https://filamentphp.com/plugins/jeffgreco-breezy) plugin.

You can publish the login page for **Filament Breezy** by running:

```bash
php artisan vendor:publish --tag="filament-breezy-views"
```

Which produces a login page at `resources/views/vendor/filament-breezy/login.blade.php`.

You can then add the following snippet in your form:

```html
<x-filament-socialite::buttons />
```

## Events

There are a few events dispatched during the authentication process:

* `InvalidState(InvalidStateException $exception)`: When trying to retrieve the oauth (socialite) user, an invalid state was encountered
* `Login(FilamentSocialiteUserContract $socialiteUser)`: When a user successfully logs in
* `Registered(FilamentSocialiteUserContract $socialiteUser)`: When a user and socialite user is successfully registered and logged in (when enabled in config)
* `RegistrationNotEnabled(string $provider, SocialiteUserContract $oauthUser)`: When a user tries to login with an unknown account and registration is not enabled
* `SocialiteUserConnected(FilamentSocialiteUserContract $socialiteUser)`: When a socialite user is created for an existing user
* `UserNotAllowed(SocialiteUserContract $oauthUser)`: When a user tries to login with an email which domain is not on the allowlist

## Scopes

Scopes can be added to the provider on the panel, for example:

```php
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Provider;

FilamentSocialitePlugin::make()
    ->providers([
        Provider::make('github')
            ->label('Github')
            ->icon('fab-github')
            ->scopes([
                // Add scopes here.
                'read:user',
                'public_repo',
            ]),
    ]),
```

## Optional parameters

You can add [optional parameters](https://laravel.com/docs/10.x/socialite#optional-parameters) to the request by adding a `with` key to the provider on the panel, for example:

```php
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Provider;

FilamentSocialitePlugin::make()
    ->providers([
        Provider::make('github')
            ->label('Github')
            ->icon('fab-github')
            ->with([
                // Add scopes here.
                // Add optional parameters here.
                'hd' => 'example.com',
            ]),
    ]),
```
## Visibility

You can set the visibility of a provider, if it is not visible, buttons will not be rendered. All functionality will still be enabled.

```php
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Provider;

FilamentSocialitePlugin::make()
    ->providers([
        Provider::make('github')
            ->visible(fn () => true),
    ]),
```

## Stateless Authentication
You can add `stateless` parameters to the provider configuration in the config/services.php config file, for example:

```php
'apple' => [
    'client_id' => '...',
    'client_secret' => '...',
    'stateless'=>true,
]
```

**Note:** you cannot use the `state` parameter, as it is used to determine from which Filament panel the user came from.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
