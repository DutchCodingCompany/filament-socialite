[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/support-ukraine.svg?t=1" />](https://supportukrainenow.org)

![](https://banners.beyondco.de/Filament%20Socialite.png?theme=light&packageManager=composer+require&packageName=DutchCodingCompany%2Ffilament-socialite&pattern=architect&style=style_1&description=Add+OAuth+login+through+Laravel+Socialite+to+Filament.&md=1&showWatermark=0&fontSize=100px&images=user-group)

# Social login for Filament through Laravel Socialite

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dutchcodingcompany/filament-socialite.svg?style=flat-square)](https://packagist.org/packages/dutchcodingcompany/filament-socialite)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/dutchcodingcompany/filament-socialite/run-tests?label=tests)](https://github.com/dutchcodingcompany/filament-socialite/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/dutchcodingcompany/filament-socialite/Check%20&%20fix%20styling?label=code%20style)](https://github.com/dutchcodingcompany/filament-socialite/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/dutchcodingcompany/filament-socialite.svg?style=flat-square)](https://packagist.org/packages/dutchcodingcompany/filament-socialite)

Add OAuth login through Laravel Socialite to Filament.

## Installation

You can install the package via composer:

```bash
composer require dutchcodingcompany/filament-socialite
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-socialite-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-socialite-config"
```

This is the contents of the published config file:

```php
return [
    // Allow login, and registration if enabled, for users with an email for one of the following domains.
    // All domains allowed by default
    'domain_allowlist' => [],

    // Allow registration through socials
    'registration' => false,

    // If you set this config to true, the `users.password` column must be nullable
    // or an error will be thrown when the user is created.
    // If you set this config to false, the user will be registered using a strong
    // random password.
    'keep_null_password' => true,

    // Specify the providers that should be visible on the login.
    // These should match the socialite providers you have setup in your services.php config.
    'providers' => [
//        'gitlab' => [
//            'label' => 'GitLab',
//            'icon' => 'fab-gitlab',
//        ],
//        'github' => [
//            'label' => 'GitHub',
//            'icon' => 'fab-github',
//        ],
    ],
];
```

### Providers

You should setup the providers with Socialite and/or [Socialite Providers](https://socialiteproviders.com/) and add them
to the providers array in the `filament-socialite.php` config.

### Icons

You can specify a Blade Icon. You can add Font Awesome brand
icons made available through [Blade Font Awesome](https://github.com/owenvoke/blade-fontawesome) by running:
```
composer require owenvoke/blade-fontawesome
```

### Registration flow

This package supports account creation for users. The default registration flow requires the `password`
attribute on your `User` model must be nullable. For example, by adding the following to your users table migration or creating a new one:

```php
$table->string('password')->nullable();
```

However, if you need to keep the user password column not nullable, you can set in the config file (filament-socialite.php) the option `keep_null_password` to false and the registration flow will create the user with a strong-random password.

```php
return [
    ...
    'keep_null_password' => true,
    ...
];
```

### Domain Allowlist

This package supports the option to limit the users that can login with the OAuth login to users of a certain domain.
This can be used to setup SSO for internal use.

### Customizing view

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-socialite-views"
```

## Usage

Add the buttons component to your login page, just above the `</form>` closing tag:

```php
    <x-filament-socialite::buttons />
</form>
```

You can publish the login page for **vanilla Filament** by running:

```bash
php artisan vendor:publish --tag="filament-views"
```

Which produces a login page at `resources/views/vendor/filament/login.blade.php`.

### Filament Fortify

This component can also be added while using the [Fortify plugin](https://filamentphp.com/plugins/fortify) plugin.

You can publish the login page for **Filament Fortify** by running:

```bash
php artisan vendor:publish --tag="filament-fortify-views"
```

Which produces a login page at `resources/views/vendor/filament-fortify/login.blade.php`.

### Filament Breezy

This component can also be added while using the [Breezy plugin](https://filamentphp.com/plugins/breezy) plugin.

You can publish the login page for **Filament Breezy** by running:

```bash
php artisan vendor:publish --tag="filament-breezy-views"
```

Which produces a login page at `resources/views/vendor/filament-breezy/login.blade.php`.

## Events

There are a few events dispatched during the authentication process:

* `DomainFailed`: When a user tries to login with an email which domain is not on the allowlist
* `RegistrationFailed`: When a user tries to login with an unknown account and registration is not enabled
* `Login`: When a user successfully logs in
* `Registered`: When a user is successfully registered and logged in (when enabled in config)

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Marco Boers](https://github.com/marcoboers)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
