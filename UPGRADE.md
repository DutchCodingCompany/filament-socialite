# Upgrade Guide
## `1.x.x` to `2.x.x` (Filament v3.x)

For version 2 we refactored most of the plugin to be more consistent with the Filament naming conventions. We've also moved some of the callbacks to the plugin, so they are configurable per panel.

### Method names

Every method name has been changed to be more consistent with the Filament naming conventions. The following changes have been made:

- `setProviders()` -> `providers()` 
- `setSlug()` -> `slug()` 
- `setLoginRouteName()` -> `loginRouteName()` 
- `setDashboardRouteName()` -> `dashboardRouteName()` 
- `setRememberLogin()` -> `rememberLogin()` 
- `setRegistrationEnabled()` -> `registration()` 
- `getRegistrationEnabled()` -> `getRegistration()` 
- `setDomainAllowList()` -> `domainAllowList()` 
- `setSocialiteUserModelClass()` -> `socialiteUserModelClass()`
- `setUserModelClass()` -> `userModelClass()` 
- `setShowDivider()` -> `showDivider()` 

**Note:** We've included a simple rector script which automatically updates the method names. It checks all panel providers in the `app\Provider\Filament` directory. You can run the script by executing the following command:

```bash
vendor/bin/upgrade-v2
```
#### Callbacks

**setCreateUserCallback()**

The `setCreateUserCallback()` has been renamed to `createUserUsing()`. This function was first registered in the `boot` method of your `AppServiceProvider.php`, but now it should be called on the plugin.

```php
FilamentSocialitePlugin::make()
    // ...
    ->createUserUsing(function (string $provider, SocialiteUserContract $oauthUser, FilamentSocialitePlugin $plugin) {
        // Logic to create a new user.
    })
```

**setUserResolver()**

The `setUserResolver()` has been renamed to `resolveUserUsing()`. This function was first registered in the `boot` method of your `AppServiceProvider.php`, but now it should be called on the plugin.

```php
FilamentSocialitePlugin::make()
    // ...
    ->resolveUserUsing(function (string $provider, SocialiteUserContract $oauthUser, FilamentSocialitePlugin $plugin) {
        // Logic to retrieve an existing user.
    })
```

**setLoginRedirectCallback()**

The `setLoginRedirectCallback()` has been renamed to `redirectAfterLoginUsing()`. This function was first registered in the `boot` method of your `AppServiceProvider.php`, but now it should be called on the plugin.

```php
FilamentSocialitePlugin::make()
    // ...
    ->redirectAfterLoginUsing(function (string $provider, FilamentSocialiteUserContract $socialiteUser, FilamentSocialitePlugin $plugin) {
        // Change the redirect behaviour here.
    })
```

#### Removals

**getOptionalParameters()**

This function was used internally only inside the `SocialiteLoginController`. If you haven't extended this controller, you can ignore this change.

Provider details can now be retrieved using `$plugin->getProvider($provider)->getWith()`.

**getProviderScopes()**

This function was used internally only inside the `SocialiteLoginController`. If you haven't extended this controller, you can ignore this change.

Provider details can now be retrieved using `$plugin->getProvider($provider)->getScopes()`.

### Configuration

**Providers**

Previously, providers were configured by passing a plain array. In the new setup, they should be created using the `Provider` class. The key should be passed as part of the `make()` function.

```php
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Provider;

FilamentSocialitePlugin::make()
    ->providers([
        Provider::make('gitlab')
            ->label('GitLab')
            ->icon('fab-gitlab')
            ->color(Color::hex('#2f2a6b')),
    ]),
```

**Scopes and Optional parameters**

Scopes and additional parameters for Socialite providers were previously configured in the `services.php` file, but have now been moved to the `->providers()` method on the Filament plugin.

```php
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Provider;

FilamentSocialitePlugin::make()
    ->providers([
        Provider::make('gitlab')
            // ...
            ->scopes([
                // Add scopes here.
                'read:user',
                'public_repo',
            ]),
            ->with([
                // Add optional parameters here.
                 'hd' => 'example.com',
            ]),
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
