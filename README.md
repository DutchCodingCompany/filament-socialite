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

| Filament version | Package version |
|------------------|-----------------|
| 3.x              | 1.x.x           |
| 2.x              | 0.x.x           |

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
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Illuminate\Contracts\Auth\Authenticatable;

// ...
->plugin(
    FilamentSocialitePlugin::make()
        // (required) Add providers corresponding with providers in `config/services.php`. 
        ->setProviders([
            'github' => [
                'label' => 'GitHub',
                // Custom icon requires an additional package, see below.
                'icon' => 'fab-github',
                // (optional) Button color override, default: 'gray'.
                'color' => 'primary',
                // (optional) Button style override, default: true (outlined).
                'outlined' => false,
            ],
        ])
        // (optional) Enable/disable registration of new (socialite-) users.
        ->setRegistrationEnabled(true)
        // (optional) Enable/disable registration of new (socialite-) users using a callback.
        // In this example, a login flow can only continue if there exists a user (Authenticatable) already.
        ->setRegistrationEnabled(fn (string $provider, SocialiteUserContract $oauthUser, ?Authenticatable $user) => (bool) $user)
        // (optional) Change the associated model class.
        ->setUserModelClass(\App\Models\User::class)
        // (optional) Change the associated socialite class (see below).
        ->setSocialiteUserModelClass(\App\Models\SocialiteUser::class)
);
```

See [Socialite Providers](https://socialiteproviders.com/) for additional Socialite providers.

### Icons

You can specify a Blade Icon. You can add Font Awesome brand
icons made available through [Blade Font Awesome](https://github.com/owenvoke/blade-fontawesome) by running:
```
composer require owenvoke/blade-fontawesome
```

### Registration flow

This package supports account creation for users. However, to support this flow it is important that the `password`
attribute on your `User` model is nullable. For example, by adding the following to your users table migration.
Or you could opt for customizing the user creation, see below.

```php
$table->string('password')->nullable();
```

### Domain Allowlist

This package supports the option to limit the users that can login with the OAuth login to users of a certain domain.
This can be used to setup SSO for internal use.

```php
->plugin(
    FilamentSocialitePlugin::make()
        ->setRegistrationEnabled(true)
        ->setDomainAllowList(['localhost'])
);
```

### Changing how an Authenticatable user is created or retrieved

In your AppServiceProvider.php, add in the boot method:
```php
use DutchCodingCompany\FilamentSocialite\Facades\FilamentSocialite as FilamentSocialiteFacade;
use DutchCodingCompany\FilamentSocialite\FilamentSocialite;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;

// Default
FilamentSocialiteFacade::setCreateUserCallback(fn (string $provider, SocialiteUserContract $oauthUser, FilamentSocialite $socialite) => $socialite->getUserModelClass()::create([
    'name' => $oauthUser->getName(),
    'email' => $oauthUser->getEmail(),
]));

FilamentSocialiteFacade::setUserResolver(fn (string $provider, SocialiteUserContract $oauthUser, FilamentSocialite $socialite) => /* ... */);
```

### Change how a Socialite user is created or retrieved

In your plugin options in your Filament panel, add the following method:
```php
// app/Providers/Filament/AdminPanelProvider.php
->plugins([
    FilamentSocialitePlugin::make()
        // ...
        ->setSocialiteUserModelClass(\App\Models\SocialiteUser::class)
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

### Multi guard/model/panel implementation

Modify socialite_users table. Update user_id to user morph column
```
   {
        Schema::create('socialite_users', function (Blueprint $table) {
            $table->id();

            $table->morphs('user');
            $table->string('provider');
            $table->string('provider_id');

            $table->timestamps();

        });
    }
```
In your plugin options in your Filament panel, add the following method:
```php
// app/Providers/Filament/AdminPanelProvider.php
->plugins([
    FilamentSocialitePlugin::make()
        // ...
        ->setSocialiteUserModelClass(DutchCodingCompany\FilamentSocialite\Models\MorphableSocialiteUser::class)
```

Lastly, you'll have to prepare your User model by adding the following interface and trait, which
will add the necessary methods to your model:

```php
use DutchCodingCompany\FilamentSocialite\Models\Concerns\MorphableSocialite;
use DutchCodingCompany\FilamentSocialite\Models\Contracts\MorphableSocialite as FilamentSocialiteContract;

// Add the interface:
class User extends Authenticatable implements FilamentSocialiteContract
{
    // Add the trait:
    use MorphableSocialite;
}
```

### Change login redirect

When your panel has [multi-tenancy](https://filamentphp.com/docs/3.x/panels/tenancy) enabled, after logging in, the user will be redirected to their [default tenant](https://filamentphp.com/docs/3.x/panels/tenancy#setting-the-default-tenant).
If you want to change this behavior, you can add the `setLoginRedirectCallback` method in the boot method of your `AppServiceProvider.php`:

```php
use DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser as FilamentSocialiteUserContract;
use DutchCodingCompany\FilamentSocialite\Models\SocialiteUser;

FilamentSocialite::setLoginRedirectCallback(function (string $provider, FilamentSocialiteUserContract $socialiteUser) {
    return redirect()->intended(
        route(FilamentSocialite::getPlugin()->getDashboardRouteName())
    );
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

Scopes should be added in your `config/services.php` config file, for example:

```php
'github' => [
    'client_id' => '...',
    'client_secret' => '...',
    'scopes' => [
        // Add scopes here.
        'read:user',
        'public_repo',
    ],
]
```

## Optional parameters

You can add [optional parameters](https://laravel.com/docs/10.x/socialite#optional-parameters) to the request by adding a `with` key to the provider configuration in the `config/services.php` config file, for example:

```php
'github' => [
    'client_id' => '...',
    'client_secret' => '...',
    'with' => [
        // Add optional parameters here
        'hd' => 'example.com',
    ],
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
