<?php

namespace DutchCodingCompany\FilamentSocialite;

use App\Models\User;
use Closure;
use DutchCodingCompany\FilamentSocialite\Exceptions\ImplementationException;
use DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser as FilamentSocialiteUserContract;
use DutchCodingCompany\FilamentSocialite\Models\SocialiteUser;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;

class FilamentSocialitePlugin implements Plugin
{
    /**
     * @var array<string, mixed>
     */
    protected array $providers = [];

    protected ?string $loginRouteName = null;

    protected ?string $dashboardRouteName = null;

    protected bool $rememberLogin = false;

    /**
     * @var \Closure(string, \Laravel\Socialite\Contracts\User, ?\Illuminate\Contracts\Auth\Authenticatable): bool|bool
     */
    protected Closure | bool $registrationEnabled = false;

    /**
     * @var array<string>
     */
    protected array $domainAllowList = [];

    /**
     * @var class-string<\Illuminate\Contracts\Auth\Authenticatable>
     */
    protected string $userModelClass = User::class;

    /**
     * @var class-string<\DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser>
     */
    protected string $socialiteUserModelClass = SocialiteUser::class;

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

    /**
     * @param array<string, mixed> $providers
     */
    public function setProviders(array $providers): static
    {
        $this->providers = $providers;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
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

    /**
     * @param \Closure(string $provider, \Laravel\Socialite\Contracts\User $oauthUser, ?\Illuminate\Contracts\Auth\Authenticatable $user): bool|bool $value
     * @return $this
     */
    public function setRegistrationEnabled(Closure | bool $value): static
    {
        $this->registrationEnabled = $value;

        return $this;
    }

    /**
     * @return \Closure(string $provider, \Laravel\Socialite\Contracts\User $oauthUser, ?\Illuminate\Contracts\Auth\Authenticatable $user): bool|bool
     */
    public function getRegistrationEnabled(): Closure | bool
    {
        return $this->registrationEnabled;
    }

    /**
     * @param array<string> $values
     */
    public function setDomainAllowList(array $values): static
    {
        $this->domainAllowList = $values;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getDomainAllowList(): array
    {
        return $this->domainAllowList;
    }

    /**
     * @param class-string<\Illuminate\Contracts\Auth\Authenticatable> $value
     */
    public function setUserModelClass(string $value): static
    {
        if (! is_a($value, Authenticatable::class, true)) {
            throw new ImplementationException('The user model class must implement the "\Illuminate\Contracts\Auth\Authenticatable" interface.');
        }

        $this->userModelClass = $value;

        return $this;
    }

    /**
     * @return class-string<\Illuminate\Contracts\Auth\Authenticatable>
     */
    public function getUserModelClass(): string
    {
        return $this->userModelClass;
    }

    /**
     * @param class-string<\DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser> $value
     */
    public function setSocialiteUserModelClass(string $value): static
    {
        if (! is_a($value, FilamentSocialiteUserContract::class, true)) {
            throw new ImplementationException('The socialite user model class must implement the "\DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser" interface.');
        }

        $this->socialiteUserModelClass = $value;

        return $this;
    }

    /**
     * @return class-string<\DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser>
     */
    public function getSocialiteUserModelClass(): string
    {
        return $this->socialiteUserModelClass;
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
