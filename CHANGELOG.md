# Changelog

All notable changes to `filament-socialite` will be documented in this file.

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
