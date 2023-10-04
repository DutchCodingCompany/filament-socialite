<?php

namespace DutchCodingCompany\FilamentSocialite;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentSocialitePlugin implements Plugin
{
    protected array $providers = [];

    protected string $loginRouteName = 'filament.admin.auth.login';

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        return filament(static::make()->getId());
    }

    public function getId(): string
    {
        return 'filament-socialite';
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public function setProviders(array $providers): static
    {
        $this->providers = $providers;

        return $this;
    }

    public function getProviders(): array
    {
        return $this->providers;
    }

    public function setLoginRouteName(string $loginRouteName): static
    {
        $this->loginRouteName = $loginRouteName;

        return $this;
    }

    public function getLoginRouteName(): string
    {
        return $this->loginRouteName;
    }
}
