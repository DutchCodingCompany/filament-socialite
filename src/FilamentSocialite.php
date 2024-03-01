<?php

namespace DutchCodingCompany\FilamentSocialite;

use App\Models\User;
use Closure;
use DutchCodingCompany\FilamentSocialite\Exceptions\GuardNotStateful;
use DutchCodingCompany\FilamentSocialite\Exceptions\ProviderNotConfigured;
use DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser as FilamentSocialiteUserContract;
use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Authenticatable;
use Filament\Panel;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Config\Repository;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;

class FilamentSocialite
{
    /**
     * @var ?\Closure(string, \Laravel\Socialite\Contracts\User, \DutchCodingCompany\FilamentSocialite\FilamentSocialite): ?(\Illuminate\Contracts\Auth\Authenticatable)
     */
    protected ?Closure $userResolver = null;

    /**
     * @deprecated This function will be removed in the next major version. Use `setSocialiteUserModelClass()` on the plugin options instead, and implement the `FilamentSocialiteUser` contract on your class.
     * @var ?\Closure(string, \Laravel\Socialite\Contracts\User, \Illuminate\Contracts\Auth\Authenticatable): \DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser
     */
    protected ?Closure $createSocialiteUserCallback = null;

    /**
     * @var ?\Closure(\Laravel\Socialite\Contracts\User, self): \Illuminate\Contracts\Auth\Authenticatable
     */
    protected ?Closure $createUserCallback = null;


    /**
     * @phpstan-var ?\Closure(\Filament\Panel $panel, \DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser $socialiteUser): \Illuminate\Http\RedirectResponse
     */
    protected ?Closure $redirectTenantCallback = null;

    protected FilamentSocialitePlugin $plugin;

    public function __construct(
        protected Repository $config,
        protected Factory $auth,
    ) {
        //
    }

    public function getPanelId(): string
    {
        return Filament::getCurrentPanel()->getId();
    }

    public function getPlugin(): FilamentSocialitePlugin
    {
        /** @var \DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin */
        return Filament::getCurrentPanel()->getPlugin('filament-socialite');
    }

    public function isProviderConfigured(string $provider): bool
    {
        return $this->config->has('services.'.$provider);
    }

    /**
     * @return array<string, mixed>
     */
    public function getProviderConfig(string $provider): array
    {
        if (! $this->isProviderConfigured($provider)) {
            throw ProviderNotConfigured::make($provider);
        }

        return $this->config->get('services.'.$provider);
    }

    /**
     * @return string|array<string>
     */
    public function getProviderScopes(string $provider): string | array
    {
        return $this->getProviderConfig($provider)['scopes'] ?? [];
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptionalParameters(string $provider): array
    {
        return $this->getProviderConfig($provider)['with'] ?? [];
    }

    /**
     * @return class-string<\Illuminate\Contracts\Auth\Authenticatable>
     */
    public function getUserModelClass(): string
    {
        return $this->getPlugin()->getUserModelClass();
    }

    public function getUserModel(): Authenticatable
    {
        return new ($this->getUserModelClass());
    }

    /**
     * @return \Closure(string $provider, \Laravel\Socialite\Contracts\User $oauthUser, \DutchCodingCompany\FilamentSocialite\FilamentSocialite $socialite): ?(\Illuminate\Contracts\Auth\Authenticatable)
     */
    public function getUserResolver(): Closure
    {
        return $this->userResolver ?? function (string $provider, SocialiteUserContract $oauthUser, FilamentSocialite $socialite) {
            /** @var \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model&\Illuminate\Contracts\Auth\Authenticatable> $model */
            $model = $this->getUserModel();

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
        return $this->getPlugin()->getSocialiteUserModelClass();
    }

    public function getSocialiteUserModel(): FilamentSocialiteUserContract
    {
        return new ($this->getSocialiteUserModelClass());
    }

    /**
     * @deprecated This function will be removed in the next major version. Use `setSocialiteUserModelClass()` on the plugin options instead, and implement the `FilamentSocialiteUser` contract on your class.
     * @param ?\Closure(string $provider, \Laravel\Socialite\Contracts\User $oauthUser, \Illuminate\Contracts\Auth\Authenticatable $user): \DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser $callback
     * @return $this
     */
    public function setCreateSocialiteUserCallback(Closure $callback = null): static
    {
        $this->createSocialiteUserCallback = $callback;

        return $this;
    }

    /**
     * @param \Closure(\Laravel\Socialite\Contracts\User $oauthUser, self $socialite): \Illuminate\Contracts\Auth\Authenticatable $callback
     */
    public function setCreateUserCallback(Closure $callback = null): static
    {
        $this->createUserCallback = $callback;

        return $this;
    }

    /**
     * @param \Closure(string $provider, \Laravel\Socialite\Contracts\User $oauthUser, \DutchCodingCompany\FilamentSocialite\FilamentSocialite $socialite): ?(\Illuminate\Contracts\Auth\Authenticatable) $callback
     */
    public function setUserResolver(Closure $callback = null): static
    {
        $this->userResolver = $callback;

        return $this;
    }

    /**
     * @deprecated This function will be removed in the next major version. Use `setSocialiteUserModelClass()` on the plugin options instead, and implement the `FilamentSocialiteUser` contract on your class.
     * @return \Closure(string $provider, \Laravel\Socialite\Contracts\User $oauthUser, \Illuminate\Contracts\Auth\Authenticatable $user): \DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser
     */
    public function getCreateSocialiteUserCallback(): Closure
    {
        return $this->createSocialiteUserCallback ?? function (
            string $provider,
            SocialiteUserContract $oauthUser,
            Authenticatable $user,
        ) {
            return $this->getSocialiteUserModel()::createForProvider($provider, $oauthUser, $user);
        };
    }

    /**
     * @return \Closure(\Laravel\Socialite\Contracts\User $oauthUser, self $socialite): \Illuminate\Contracts\Auth\Authenticatable
     */
    public function getCreateUserCallback(): Closure
    {
        return $this->createUserCallback ?? function (
            SocialiteUserContract $oauthUser,
            FilamentSocialite $socialite,
        ) {
            /**
             * @var \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model&\Illuminate\Contracts\Auth\Authenticatable> $query
             */
            $query = $this->getUserModel()->query();

            return $query->create([
                'name' => $oauthUser->getName(),
                'email' => $oauthUser->getEmail(),
            ]);
        };
    }

    public function getGuard(): StatefulGuard
    {
        $guard = $this->auth->guard(
            $guardName = Filament::getCurrentPanel()->getAuthGuard()
        );

        if ($guard instanceof StatefulGuard) {
            return $guard;
        }

        throw GuardNotStateful::make($guardName);
    }

    /**
     * @param \Closure(\Filament\Panel $panel, \DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser $socialiteUser): \Illuminate\Http\RedirectResponse $callback
     */
    public function setRedirectTenantCallback(Closure $callback): static
    {
        $this->redirectTenantCallback = $callback;

        return $this;
    }

    /**
     * @return \Closure(\Filament\Panel $panel, \DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser $socialiteUser): \Illuminate\Http\RedirectResponse
     */
    public function getRedirectTenantCallback(): Closure
    {
        return $this->redirectTenantCallback ?? function (Panel $panel, FilamentSocialiteUserContract $socialiteUser) {
            $tenant = Filament::getUserDefaultTenant($socialiteUser->getUser());

            if (is_null($tenant) && $tenantRegistrationUrl = $panel->getTenantRegistrationUrl()) {
                return redirect()->intended($tenantRegistrationUrl);
            }

            return redirect()->intended(
                $panel->getUrl($tenant)
            );
        };
    }
}
