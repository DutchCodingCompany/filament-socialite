<?php

namespace DutchCodingCompany\FilamentSocialite;

use App\Models\User;
use Closure;
use DutchCodingCompany\FilamentSocialite\Exceptions\ImplementationException;
use DutchCodingCompany\FilamentSocialite\Exceptions\ProviderNotConfigured;
use DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser as FilamentSocialiteUserContract;
use DutchCodingCompany\FilamentSocialite\Models\SocialiteUser;
use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;

class FilamentSocialitePlugin implements Plugin
{
    use Traits\Callbacks;
    use Traits\Routes;

    /**
     * @var array<string, mixed>
     */
    protected array $providers = [];

    protected bool $rememberLogin = false;

    /**
     * @phpstan-var (\Closure(string $provider, \Laravel\Socialite\Contracts\User $oauthUser, ?\Illuminate\Contracts\Auth\Authenticatable $user): bool) | bool
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

    public function __construct(protected Repository $config)
    {
        //
    }

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

    /**
     * @param class-string<\Illuminate\Contracts\Auth\Authenticatable> $value
     * @throws ImplementationException
     */
    public function userModelClass(string $value): static
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
    public function socialiteUserModelClass(string $value): static
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

    public function getSocialiteUserModel(): FilamentSocialiteUserContract
    {
        return new ($this->getSocialiteUserModelClass());
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
}
