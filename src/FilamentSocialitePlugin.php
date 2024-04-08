<?php

namespace DutchCodingCompany\FilamentSocialite;

use Closure;
use DutchCodingCompany\FilamentSocialite\Exceptions\GuardNotStateful;
use DutchCodingCompany\FilamentSocialite\Exceptions\ImplementationException;
use DutchCodingCompany\FilamentSocialite\Exceptions\ProviderNotConfigured;
use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Str;

class FilamentSocialitePlugin implements Plugin
{
    use Traits\Callbacks;
    use Traits\Routes;
    use Traits\Models;

    /**
     * @var array<string, mixed>
     */
    protected array $providers = [];

    /**
     * @var array<string>
     */
    protected array $domainAllowList = [];

    protected bool $rememberLogin = false;

    /**
     * @phpstan-var (\Closure(string $provider, \Laravel\Socialite\Contracts\User $oauthUser, ?\Illuminate\Contracts\Auth\Authenticatable $user): bool) | bool
     */
    protected Closure | bool $registrationEnabled = false;

    protected ?string $slug = null;

    protected bool $showDivider = true;

    public function __construct(
        protected Repository $config,
        protected Factory $auth,
    ) {
        //
    }

    public static function make(): static
    {
        $instance = app(static::class);

        app()->singleton(static::class, fn () => $instance);

        return $instance;
    }

    public function getId(): string
    {
        return 'filament-socialite';
    }

    public function register(Panel $panel): void
    {
        if ($this->slug === null) {
            $this->slug(Str::slug($panel->getId()));
        }

        if ($this->loginRouteName === null) {
            $this->loginRouteName("filament.{$panel->getId()}.auth.login");
        }

        if ($this->dashboardRouteName === null) {
            $this->dashboardRouteName("filament.{$panel->getId()}.pages.dashboard");
        }
    }

    public function boot(Panel $panel): void
    {
        //
    }

    /**
     * @param array<string, mixed> $providers
     */
    public function providers(array $providers): static
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

    public function slug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug(): string
    {
        assert(is_string($this->slug));

        return $this->slug;
    }

    public function rememberLogin(bool $value): static
    {
        $this->rememberLogin = $value;

        return $this;
    }

    public function getRememberLogin(): bool
    {
        return $this->rememberLogin;
    }

    /**
     * @param (\Closure(string $provider, \Laravel\Socialite\Contracts\User $oauthUser, ?\Illuminate\Contracts\Auth\Authenticatable $user): bool) | bool $value
     * @return $this
     */
    public function registrationEnabled(Closure | bool $value): static
    {
        $this->registrationEnabled = $value;

        return $this;
    }

    /**
     * @return (\Closure(string $provider, \Laravel\Socialite\Contracts\User $oauthUser, ?\Illuminate\Contracts\Auth\Authenticatable $user): bool) | bool
     */
    public function getRegistrationEnabled(): Closure | bool
    {
        return $this->registrationEnabled;
    }

    /**
     * @param array<string> $values
     */
    public function domainAllowList(array $values): static
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

    public function isProviderConfigured(string $provider): bool
    {
        return $this->config->has('services.'.$provider) && isset($this->providers[$provider]);
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptionalParameters(string $provider): array
    {
        if (! $this->isProviderConfigured($provider)) {
            throw ProviderNotConfigured::make($provider);
        }

        return $this->providers[$provider]['with'] ?? [];
    }

    /**
     * @return string|array<string>
     */
    public function getProviderScopes(string $provider): string | array
    {
        if (! $this->isProviderConfigured($provider)) {
            throw ProviderNotConfigured::make($provider);
        }

        return $this->providers[$provider]['scopes'] ?? [];
    }

    public function showDivider(bool $divider): static
    {
        $this->showDivider = $divider;

        return $this;
    }

    public function getShowDivider(): bool
    {
        return $this->showDivider;
    }

    public function getPanel(): Panel
    {
        return Filament::getCurrentPanel() ?? throw new ImplementationException('No panel is currently set.');
    }

    public function getGuard(): StatefulGuard
    {
        $guard = $this->auth->guard(
            $guardName = $this->getPanel()->getAuthGuard()
        );

        if ($guard instanceof StatefulGuard) {
            return $guard;
        }

        throw GuardNotStateful::make($guardName);
    }
}
