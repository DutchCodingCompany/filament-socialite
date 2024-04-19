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
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class FilamentSocialitePlugin implements Plugin
{
    use Traits\Callbacks;
    use Traits\Routes;
    use Traits\Models;

    /**
     * @var array<string, \DutchCodingCompany\FilamentSocialite\Provider>
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
    protected Closure | bool $registration = false;

    protected ?string $slug = null;

    protected ?string $panelId = null;

    protected bool $showDivider = true;

    public function __construct(
        protected Repository $config,
        protected Factory $auth,
    ) {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function current(): static
    {
        if (Filament::getCurrentPanel()?->hasPlugin('filament-socialite')) {
            /** @var static $plugin */
            $plugin = Filament::getCurrentPanel()->getPlugin('filament-socialite');

            return $plugin;
        }

        throw new ImplementationException('No current panel found with filament-socialite plugin.');
    }

    public function getId(): string
    {
        return 'filament-socialite';
    }

    public function register(Panel $panel): void
    {
        $this->panelId = $panel->getId();
    }

    public function boot(Panel $panel): void
    {
        //
    }

    /**
     * @param array<array-key, \DutchCodingCompany\FilamentSocialite\Provider> $providers
     */
    public function providers(array $providers): static
    {
        // Assign providers as key-value pairs with the provider name as the key.
        $this->providers = Arr::mapWithKeys(
            $providers,
            static fn (Provider $value) => [$value->getName() => $value],
        );

        return $this;
    }

    /**
     * @return array<string, \DutchCodingCompany\FilamentSocialite\Provider>
     */
    public function getProviders(): array
    {
        return $this->providers;
    }

    public function getProvider(string $provider): Provider
    {
        if (! $this->isProviderConfigured($provider)) {
            throw ProviderNotConfigured::make($provider);
        }

        return $this->providers[$provider];
    }

    public function slug(?string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug ?? Str::slug($this->getPanelId());
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
    public function registration(Closure | bool $value = true): static
    {
        $this->registration = $value;

        return $this;
    }

    /**
     * @return (\Closure(string $provider, \Laravel\Socialite\Contracts\User $oauthUser, ?\Illuminate\Contracts\Auth\Authenticatable $user): bool) | bool
     */
    public function getRegistration(): Closure | bool
    {
        return $this->registration;
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
        return Filament::getPanel($this->getPanelId());
    }

    public function getPanelId(): string
    {
        return $this->panelId ?? throw new ImplementationException('Panel ID not set.');
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
