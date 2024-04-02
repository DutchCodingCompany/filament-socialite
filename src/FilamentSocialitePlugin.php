<?php

namespace DutchCodingCompany\FilamentSocialite;

use App\Models\User;
use Closure;
use DutchCodingCompany\FilamentSocialite\Exceptions\ImplementationException;
use DutchCodingCompany\FilamentSocialite\Exceptions\ProviderNotConfigured;
use DutchCodingCompany\FilamentSocialite\Facades\FilamentSocialite;
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
    /**
     * @var array<string, mixed>
     */
    protected array $providers = [];

    protected ?string $loginRouteName = null;

    protected ?string $dashboardRouteName = null;

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

    /**
     * @phpstan-var ?\Closure(string $provider, \Laravel\Socialite\Contracts\User $oauthUser, \DutchCodingCompany\FilamentSocialite\FilamentSocialite $socialite): ?(\Illuminate\Contracts\Auth\Authenticatable)
     */
    protected ?Closure $userResolver = null;

    /**
     * @phpstan-var ?\Closure(string $provider, \Laravel\Socialite\Contracts\User $oauthUser, self $socialite): \Illuminate\Contracts\Auth\Authenticatable
     */
    protected ?Closure $createUserCallback = null;

    /**
     * @phpstan-var ?\Closure(string $provider, \DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser $socialiteUser): \Illuminate\Http\RedirectResponse
     */
    protected ?Closure $loginRedirectCallback = null;

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
     * @param (\Closure(string $provider, \Laravel\Socialite\Contracts\User $oauthUser, ?\Illuminate\Contracts\Auth\Authenticatable $user): bool) | bool $value
     * @return $this
     */
    public function setRegistrationEnabled(Closure | bool $value): static
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
     * @throws ImplementationException
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

    public function setCreateUserCallback(Closure $callback = null): static
    {
        $this->createUserCallback = $callback;

        return $this;
    }

    /**
     * @return \Closure(string $provider, \Laravel\Socialite\Contracts\User $oauthUser, self $socialite): \Illuminate\Contracts\Auth\Authenticatable
     */
    public function getCreateUserCallback(): Closure
    {
        return $this->createUserCallback ?? function (
            string $provider,
            SocialiteUserContract $oauthUser,
            FilamentSocialite $socialite,
        ) {
            /**
             * @var \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model&\Illuminate\Contracts\Auth\Authenticatable> $query
             */
            $query = (new $this->userModelClass())->query();

            return $query->create([
                'name' => $oauthUser->getName(),
                'email' => $oauthUser->getEmail(),
            ]);
        };
    }

    public function setUserResolver(Closure $callback = null): static
    {
        $this->userResolver = $callback;

        return $this;
    }

    /**
     * @return \Closure(string $provider, \Laravel\Socialite\Contracts\User $oauthUser, \DutchCodingCompany\FilamentSocialite\FilamentSocialite $socialite): ?(\Illuminate\Contracts\Auth\Authenticatable)
     */
    public function getUserResolver(): Closure
    {
        return $this->userResolver ?? function (string $provider, SocialiteUserContract $oauthUser, $socialite) {
            /** @var \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model&\Illuminate\Contracts\Auth\Authenticatable> $model */
            $model = (new $this->userModelClass());

            return $model->where(
                'email',
                $oauthUser->getEmail()
            )->first();
        };
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

    public function setShowDivider(bool $divider): static
    {
        $this->showDivider = $divider;

        return $this;
    }

    /**
     * @param \Closure(string $provider, \DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser $socialiteUser): \Illuminate\Http\RedirectResponse $callback
     */
    public function setLoginRedirectCallback(Closure $callback): static
    {
        $this->loginRedirectCallback = $callback;

        return $this;
    }

    /**
     * @return \Closure(string $provider, \DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser $socialiteUser): \Illuminate\Http\RedirectResponse
     */
    public function getLoginRedirectCallback(): Closure
    {
        return $this->loginRedirectCallback ?? function (string $provider, FilamentSocialiteUserContract $socialiteUser) {
            if (($panel = Filament::getCurrentPanel())->hasTenancy()) {
                $tenant = Filament::getUserDefaultTenant($socialiteUser->getUser());

                if (is_null($tenant) && $tenantRegistrationUrl = $panel->getTenantRegistrationUrl()) {
                    return redirect()->intended($tenantRegistrationUrl);
                }

                return redirect()->intended(
                    $panel->getUrl($tenant)
                );
            }

            return redirect()->intended(
                route($this->getDashboardRouteName())
            );
        };
    }

    public function getShowDivider(): bool
    {
        return $this->showDivider;
    }
}
