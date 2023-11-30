# Upgrade Guide
## `0.x.x` to `1.x.x` (Filament v3.x)
- Replace/republish the configuration file:
  - `sail artisan vendor:publish --provider="DutchCodingCompany\FilamentSocialite\FilamentSocialiteServiceProvider"`
- Update your panel configuration `App\Providers\Filament\YourPanelProvider` to include the plugin:
  - Append `->plugins([\DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin::make()])`
  - Configure any options by chaining functions on the plugin.

## `0.x.x` (Filament v2.x)
- Initial version
