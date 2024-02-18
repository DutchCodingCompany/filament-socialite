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
        // (optional) Enable or disable registration from OAuth.
        ->setRegistrationEnabled(true)
        // (optional) Change the associated model class.
        ->setUserModelClass(\App\Models\User::class)
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

Add morphMany relationship to your Authenticable Models

```
use DutchCodingCompany\FilamentSocialite\Models\SocialiteUser;

public function socials(): MorphMany
{
    return $this->morphMany(SocialiteUser::class, 'user');
}
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

### Changing how a (socialite) user is created or retrieved

In your AppServiceProvider.php, add in the boot method
```php
use DutchCodingCompany\FilamentSocialite\Facades\FilamentSocialite as FilamentSocialiteFacade;
use DutchCodingCompany\FilamentSocialite\FilamentSocialite;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;

// Default
FilamentSocialiteFacade::setCreateUserCallback(fn (SocialiteUserContract $oauthUser, FilamentSocialite $socialite) => $socialite->getUserModelClass()::create([
    'name' => $oauthUser->getName(),
    'email' => $oauthUser->getEmail(),
]));
```

One can set a callback to customize the following actions:
* Create the filament user: `FilamentSocialite::setCreateUserCallback()`
* Create the socialite user: `FilamentSocialite::setCreateSocialiteUserCallback()`
* Resolve the regular user: `FilamentSocialite::setUserResolver()`

See [FilamentSocialite.php](src/FilamentSocialite.php).

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

* `Login`: When a user successfully logs in
* `Registered`: When a user is successfully registered and logged in (when enabled in config)
* `UserNotAllowed`: When a user tries to login with an email which domain is not on the allowlist
* `RegistrationNotEnabled`: When a user tries to login with an unknown account and registration is not enabled
* `InvalidState`: When trying to retrieve the oauth (socialite) user, an invalid state was encountered

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
