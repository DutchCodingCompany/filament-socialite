<?php

namespace DutchCodingCompany\FilamentSocialite;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentSocialitePlugin implements Plugin
{
    protected array $providers = [];

    protected string $loginRouteName = 'filament.admin.auth.login';

    protected string $dashboardRouteName = 'filament.admin.pages.dashboard';

    protected bool $rememberLogin = false;

    protected bool $registrationEnabled = false;

    protected array $domainAllowList = [];

    protected string $userModelClass = \App\Models\User::class;

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

    public function setLoginRouteName(string $value): static
    {
        $this->loginRouteName = $value;

        return $this;
    }

    public function getLoginRouteName(): string
    {
        return $this->loginRouteName;
    }

    public function setDashboardRouteName(string $value): static
    {
        $this->dashboardRouteName = $value;

        return $this;
    }

    public function getDashboardRouteName(): string
    {
        return $this->dashboardRouteName;
    }

    public function setRememberLogin(bool $value): static
    {
        $this->rememberLogin = $value;

        return $this;
    }

    public function getRememberLogin(): bool
    {
        return $this->rememberLogin;
    }

    public function setRegistrationEnabled(bool $value): static
    {
        $this->registrationEnabled = $value;

        return $this;
    }

    public function getRegistrationEnabled(): bool
    {
        return $this->registrationEnabled;
    }

    public function setDomainAllowList(array $values): static
    {
        $this->domainAllowList = $values;

        return $this;
    }

    public function getDomainAllowList(): array
    {
        return $this->domainAllowList;
    }

    public function setUserModelClass(string $value): static
    {
        $this->userModelClass = $value;

        return $this;
    }

    public function getUserModelClass(): string
    {
        return $this->userModelClass;
    }
}
