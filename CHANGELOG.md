# Changelog

All notable changes to `filament-socialite` will be documented in this file.

## [3.0.0-beta3 - 2025-07-18](https://github.com/DutchCodingCompany/filament-socialite/compare/3.0.0-beta2...3.0.0-beta3)

## What's Changed
* Include compiled styles
  
## [3.0.0-beta2 - 2025-06-23](https://github.com/DutchCodingCompany/filament-socialite/compare/3.0.0-alpha1...3.0.0-beta2)

## What's Changed
* BREAKING CHANGE: Implement fix for slug issue https://github.com/DutchCodingCompany/filament-socialite/issues/127  
  The package now uses `path` instead of `id` as default prefix as it should have done. In order to revert to previous behaviour, use slug to override the behaviour:
  ```php
  ->plugin(
      FilamentSocialitePlugin::make()
          ->slug('admin') // change this to the panel's ID
          // other config for plugin
  )
  ```

## [3.0.0-alpha1 - 2025-06-05](https://github.com/DutchCodingCompany/filament-socialite/compare/2.4.0...3.0.0-alpha1) / [3.0.0-beta1 - 2025-06-05](https://github.com/DutchCodingCompany/filament-socialite/compare/2.4.0...3.0.0-beta1)

## What's Changed
* Filament V4 support by @erikgaal in https://github.com/DutchCodingCompany/filament-socialite/pull/131

## [2.4.0 - 2025-02-25](https://github.com/DutchCodingCompany/filament-socialite/compare/2.3.1...2.4.0)

## What's Changed
* Laravel 12.x Compatibility by @laravel-shift in https://github.com/DutchCodingCompany/filament-socialite/pull/125

## [2.3.1 - 2025-02-06](https://github.com/DutchCodingCompany/filament-socialite/compare/2.3.0...2.3.1)

## What's Changed
* Bump dependabot/fetch-metadata from 2.2.0 to 2.3.0 by @dependabot in https://github.com/DutchCodingCompany/filament-socialite/pull/123
* Add data to events by @dododedodonl in https://github.com/DutchCodingCompany/filament-socialite/pull/124

## [2.3.0 - 2024-11-29](https://github.com/DutchCodingCompany/filament-socialite/compare/2.2.1...2.3.0)

## What's Changed
* Add option to hide providers by @dododedodonl in https://github.com/DutchCodingCompany/filament-socialite/pull/122
* Add support for php 8.4

## [2.2.1 - 2024-07-17](https://github.com/DutchCodingCompany/filament-socialite/compare/2.2.0...2.2.1)

## What's Changed
* Revert model property changes by @bert-w in https://github.com/DutchCodingCompany/filament-socialite/pull/110

## [2.2.0 - 2024-07-15](https://github.com/DutchCodingCompany/filament-socialite/compare/2.1.1...2.2.0)

## What's Changed
* Bump dependabot/fetch-metadata from 2.1.0 to 2.2.0 by @dependabot in https://github.com/DutchCodingCompany/filament-socialite/pull/106
* Add new callback route for stateless OAuth flows by @bert-w in https://github.com/DutchCodingCompany/filament-socialite/pull/105

## [2.1.1 - 2024-06-21](https://github.com/DutchCodingCompany/filament-socialite/compare/2.1.0...2.1.1)

## What's Changed
* Improve Socialite driver typings + callable typings by @juliangums in https://github.com/DutchCodingCompany/filament-socialite/pull/103

## New Contributors
* @juliangums made their first contribution in https://github.com/DutchCodingCompany/filament-socialite/pull/103

## [2.1.0 - 2024-06-21](https://github.com/DutchCodingCompany/filament-socialite/compare/2.0.0...2.1.0)

* Add Authorization Callback by @petecoop in https://github.com/DutchCodingCompany/filament-socialite/pull/100

## [2.0.0 - 2024-06-04](https://github.com/DutchCodingCompany/filament-socialite/compare/1.5.0...2.0.0)
* **Please check the revised [README.md](https://github.com/DutchCodingCompany/filament-socialite/blob/main/README.md) and [UPGRADE.md](https://github.com/DutchCodingCompany/filament-socialite/blob/main/UPGRADE.md)! Many functions have been renamed.**
* Refactor package for better consistency with Filament code standards https://github.com/DutchCodingCompany/filament-socialite/pull/90

## [1.5.0 - 2024-06-04](https://github.com/DutchCodingCompany/filament-socialite/compare/1.4.1...1.5.0)

* Bump dependabot/fetch-metadata from 1.6.0 to 2.0.0 by @dependabot in https://github.com/DutchCodingCompany/filament-socialite/pull/89
* Bump dependabot/fetch-metadata from 2.0.0 to 2.1.0 by @dependabot in https://github.com/DutchCodingCompany/filament-socialite/pull/91
* Compatible with Stateless Authentication by @LittleHans8 in https://github.com/DutchCodingCompany/filament-socialite/pull/96

## [1.4.1 - 2024-03-20](https://github.com/DutchCodingCompany/filament-socialite/compare/v1.4.0...1.4.1)
* Provide oauth user to login event by @dcc-bjorn in https://github.com/DutchCodingCompany/filament-socialite/pull/88

## [1.4.0 - 2024-03-12](https://github.com/DutchCodingCompany/filament-socialite/compare/v1.3.1...1.4.0)
* Laravel 11 support by @dododedodonl in https://github.com/DutchCodingCompany/filament-socialite/pull/87

## [1.3.1 - 2024-03-05](https://github.com/DutchCodingCompany/filament-socialite/compare/v1.3.0...1.3.1)
* Add $provider as required by the callback by @phh in https://github.com/DutchCodingCompany/filament-socialite/pull/83
* Never use SPA mode for oauth links + spacing by @bert-w in https://github.com/DutchCodingCompany/filament-socialite/pull/85

## [1.3.0 - 2024-03-01](https://github.com/DutchCodingCompany/filament-socialite/compare/v1.2.0...1.3.0)
* Update CHANGELOG.md by @bramr94 in https://github.com/DutchCodingCompany/filament-socialite/pull/70
* Add socialite test by @bert-w in https://github.com/DutchCodingCompany/filament-socialite/pull/78
* Improve actions by @bert-w in https://github.com/DutchCodingCompany/filament-socialite/pull/79
* Bump stefanzweifel/git-auto-commit-action from 4 to 5 by @dependabot in https://github.com/DutchCodingCompany/filament-socialite/pull/51
* feature: allow socialite user model customization by @kykurniawan in https://github.com/DutchCodingCompany/filament-socialite/pull/72
* Add registration enabled callable by @bert-w in https://github.com/DutchCodingCompany/filament-socialite/pull/80
* Multi-tenancy support by @bramr94 in https://github.com/DutchCodingCompany/filament-socialite/pull/76

## [1.2.0 - 2024-01-31](https://github.com/DutchCodingCompany/filament-socialite/compare/v1.1.1...1.2.0)
- Add option to add optional parameters in https://github.com/DutchCodingCompany/filament-socialite/pull/69

## [1.1.1 - 2024-01-18](https://github.com/DutchCodingCompany/filament-socialite/compare/1.1.0...1.1.1)
- Improve domain routing in https://github.com/DutchCodingCompany/filament-socialite/pull/61
- Update README in https://github.com/DutchCodingCompany/filament-socialite/pull/64

## [1.1.0 - 2024-01-08](https://github.com/DutchCodingCompany/filament-socialite/compare/1.0.1...1.1.0)
- Add button customization options in https://github.com/DutchCodingCompany/filament-socialite/pull/59

## [1.0.1 - 2023-12-18](https://github.com/DutchCodingCompany/filament-socialite/compare/1.0.0...1.0.1)
- Resolve plugin registration issue [#54](https://github.com/DutchCodingCompany/filament-socialite/issues/54)

## [1.0.0 - 2023-12-05](https://github.com/DutchCodingCompany/filament-socialite/compare/0.2.2...1.0.0)
- Added support for Filament v3 through the plugin setup
- Added support for multiple panels
- See [UPGRADE.md](UPGRADE.md)

## 0.2.2 - 2022-06-14

### What's Changed

- Fix readme by @dododedodonl in https://github.com/DutchCodingCompany/filament-socialite/pull/15
- use Filament-fortify render hook by @wychoong in https://github.com/DutchCodingCompany/filament-socialite/pull/16

### New Contributors

- @wychoong made their first contribution in https://github.com/DutchCodingCompany/filament-socialite/pull/16

**Full Changelog**: https://github.com/DutchCodingCompany/filament-socialite/compare/0.2.1...0.2.2

## 0.2.1 - 2022-05-25

## What's Changed

- Fix user model instantiating by @marcoboers in https://github.com/DutchCodingCompany/filament-socialite/pull/14

**Full Changelog**: https://github.com/DutchCodingCompany/filament-socialite/compare/0.2.0...0.2.1

## 0.2.0 - 2022-05-24

## Breaking changes

- `Events\DomainFailed` renamed to `Events\UserNotAllowed`
- `Events\RegistrationFailed` renamed to `Events\RegistrationNotEnabled`

## What's Changed

- Refactor the controller for extendability and customization by @dododedodonl in https://github.com/DutchCodingCompany/filament-socialite/pull/13

## New Contributors

- @dododedodonl made their first contribution in https://github.com/DutchCodingCompany/filament-socialite/pull/13

**Full Changelog**: https://github.com/DutchCodingCompany/filament-socialite/compare/0.1.5...0.2.0

## 0.1.5 - 2022-05-20

## What's Changed

- Fix missing variable for registered event by @marcoboers in https://github.com/DutchCodingCompany/filament-socialite/pull/11

**Full Changelog**: https://github.com/DutchCodingCompany/filament-socialite/compare/0.1.4...0.1.5

## 0.1.4 - 2022-05-06

## What's Changed

- Feature: Adds buttons blade component by @oyepez003 in https://github.com/DutchCodingCompany/filament-socialite/pull/8
- Feature: Add login events dispatching by @marcoboers in https://github.com/DutchCodingCompany/filament-socialite/pull/5

**Full Changelog**: https://github.com/DutchCodingCompany/filament-socialite/compare/0.1.3...0.1.4

## 0.1.3 - 2022-05-04

## What's Changed

- Bugfix: Avoid returning 403 when a user exists based on the oauth-email . by @oyepez003 in https://github.com/DutchCodingCompany/filament-socialite/pull/7

## New Contributors

- @oyepez003 made their first contribution in https://github.com/DutchCodingCompany/filament-socialite/pull/7

**Full Changelog**: https://github.com/DutchCodingCompany/filament-socialite/compare/0.1.2...0.1.3

## 0.1.2 - 2022-05-03

## What's Changed

- Bump dependabot/fetch-metadata from 1.3.0 to 1.3.1 by @dependabot in https://github.com/DutchCodingCompany/filament-socialite/pull/2
- Add Laravel 8 support and make fontawesome icons optional by @marcoboers in https://github.com/DutchCodingCompany/filament-socialite/pull/4

## New Contributors

- @marcoboers made their first contribution in https://github.com/DutchCodingCompany/filament-socialite/pull/4

**Full Changelog**: https://github.com/DutchCodingCompany/filament-socialite/compare/0.1.1...0.1.2

## 0.1.1 - 2022-04-11

## What's Changed

- Fix registration flow

## 0.1.0 - 2022-04-08

### Initial Release

- Add social login links to login page
- Support Socialite OAuth flow
- Support registration flow
- Support domain allowlist for internal use
- Dark mode support
- Blade Font Awesome brand icons
