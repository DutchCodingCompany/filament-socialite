<?php

namespace DutchCodingCompany\FilamentSocialite;

use App\Models\User;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FilamentSocialitePlugin implements Plugin
{
    protected array $providers = [];

    protected ?string $loginRouteName = null;

    protected ?string $dashboardRouteName = null;

    protected bool $rememberLogin = false;

    protected bool $registrationEnabled = false;

    protected array $domainAllowList = [];

    protected string $userModelClass = User::class;

    protected ?string $slug = null;

    protected bool $showDivider = true;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-socialite';
    }

    public function register(Panel $panel): void
    {
        if ($this->slug === null) {
            $this->setSlug(Str::slug($panel->getId()));
        }

        if ($this->loginRouteName === null) {
            $this->setLoginRouteName("filament.{$panel->getId()}.auth.login");
        }

        if ($this->dashboardRouteName === null) {
            $this->setDashboardRouteName("filament.{$panel->getId()}.pages.dashboard");
        }
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

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getRoute(): string
    {
        return "socialite.$this->slug.oauth.redirect";
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

    /**
     * @param class-string<Model> $value
     */
    public function setUserModelClass(string $value): static
    {
        $this->userModelClass = $value;

        return $this;
    }

    /**
     * @return class-string<Model>
     */
    public function getUserModelClass(): string
    {
        return $this->userModelClass;
    }

    public function setShowDivider(bool $divider): static
    {
        $this->showDivider = $divider;

        return $this;
    }

    public function getShowDivider(): bool
    {
        return $this->showDivider;
    }
}
