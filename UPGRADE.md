# Upgrade Guide
## `1.x.x` to `2.x.x`

For version 2 we refactored most of the plugin to be more consistent with the Filament naming conventions. We've also moved some of the callbacks to the plugin, so they are configurable per panel.

### Method names

Every method name has been changed to be more consisten with the Filament naming conventions. The following changes have been made:

- `setProviders` -> `providers`
- `setSlug` -> `slug`
- `setLoginRouteName` -> `loginRouteName`
- `setDashboardRouteName` -> `dashboardRouteName`
- `setRememberLogin` -> `rememberLogin`
- `setRegistrationEnabled` -> `registration`
- `getRegistrationEnabled` -> `getRegistration`
- `setDomainAllowList` -> `domainAllowList`
- `setSocialiteUserModelClass` -> `socialiteUserModelClass
- `setUserModelClass` -> `userModelClass`
- `setShowDivider` -> `showDivider`

### Callbacks

**setCreateUserCallback**

The `setCreateUserCallback` has been renamed to `createUserUsing`. This function was first registered in the `boot` method of your `AppServiceProvider.php`, but now it should be called on the plugin.

```php
FilamentSocialitePlugin::make()
    // ...
    ->createUserUsing(function (string $provider, SocialiteUserContract $oauthUser, FilamentSocialitePlugin $plugin) {
        // Logic to create a new user.
    })
```

**setUserResolver**

The `setUserResolver` has been renamed to `resolveUserUsing`. This function was first registered in the `boot` method of your `AppServiceProvider.php`, but now it should be called on the plugin.

```php
FilamentSocialitePlugin::make()
    // ...
    ->resolveUserUsing(function (string $provider, SocialiteUserContract $oauthUser, FilamentSocialitePlugin $plugin) {
        // Logic to retrieve an existing user.
    })
```

**setLoginRedirectCallback**

The `setLoginRedirectCallback` has been renamed to `redirectAfterLoginUsing`. This function was first registered in the `boot` method of your `AppServiceProvider.php`, but now it should be called on the plugin.

```php
FilamentSocialitePlugin::make()
    // ...
    ->redirectAfterLoginUsing(function (string $provider, FilamentSocialiteUserContract $socialiteUser, FilamentSocialitePlugin $plugin) {
        // Change the redirect behaviour here.
    })
```

### Configuration

**Optional parameters**

These where first configured in the `services.php` file, but now they should be configured in the `providers` method.

```php
FilamentSocialitePlugin::make()
    ->providers([
        'github' => [
            'label' => 'Github',
            'icon' => 'fab-github',
            'with' => [
                // Add optional parameters here.
                'hd' => 'example.com',
            ],
        ],
    ]),
```

**Scopes**

Scopes where first configured in the `services.php` file, but now they should be configured in the `providers` method.

```php
FilamentSocialitePlugin::make()
    ->providers([
        'github' => [
            'label' => 'Github',
            'icon' => 'fab-github',
            'scopes' => [
                // Add scopes here.
                'read:user',
                'public_repo',
            ],
        ],
    ]),
```

## `0.x.x` to `1.x.x` (Filament v3.x)
- Replace/republish the configuration file:
  - `sail artisan vendor:publish --provider="DutchCodingCompany\FilamentSocialite\FilamentSocialiteServiceProvider"`
- Update your panel configuration `App\Providers\Filament\YourPanelProvider` to include the plugin:
  - Append `->plugins([\DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin::make()])`
  - Configure any options by chaining functions on the plugin.

## `0.x.x` (Filament v2.x)
- Initial version
