<?php

namespace DutchCodingCompany\FilamentSocialite;

use Closure;
use DutchCodingCompany\FilamentSocialite\Exceptions\ProviderNotConfigured;
use DutchCodingCompany\FilamentSocialite\Models\SocialiteUser;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Eloquent\Model;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;

class FilamentSocialite
{
    protected ?Closure $userResolver = null;

    protected ?Closure $createSocialiteUserCallback = null;

    protected ?Closure $createUserCallback = null;

    public function __construct(
        protected Repository $config,
        protected Factory $auth,
    ) {
    }

    public function isProviderConfigured(string $provider): bool
    {
        return $this->config->has('services.'.$provider);
    }

    public function getProviderConfig(string $provider): array
    {
        if (! $this->isProviderConfigured($provider)) {
            throw ProviderNotConfigured::make($provider);
        }

        return $this->config->get('services.'.$provider);
    }

    public function getProviderScopes(string $provider): string|array
    {
        return $this->getProviderConfig($provider)['scopes'] ?? [];
    }

    public function getConfig(): array
    {
        return $this->config->get('filament-socialite', []);
    }

    public function getProviderButtons(): array
    {
        return $this->getConfig()['providers'] ?? [];
    }

    public function getDomainAllowList(): array
    {
        return $this->getConfig()['domain_allowlist'] ?? [];
    }

    public function getLoginRedirectRoute(): string
    {
        return $this->getConfig()['login_redirect_route'] ?? 'filament.pages.dashboard';
    }

    public function getUserModelClass(): string
    {
        return $this->getConfig()['user_model'] ?? \App\Models\User::class;
    }

    public function getUserModel(): Model
    {
        return new ($this->getUserModelClass());
    }

    public function getUserResolver(): Closure
    {
        return $this->userResolver ?? fn (SocialiteUserContract $oauthUser) => $this->getUserModel()->where('email', $oauthUser->getEmail())->first();
    }

    public function setCreateSocialiteUserCallback(Closure $callback = null): static
    {
        $this->createSocialiteUserCallback = $callback;

        return $this;
    }

    public function setCreateUserCallback(Closure $callback = null): static
    {
        $this->createUserCallback = $callback;

        return $this;
    }

    public function setUserResolver(Closure $callback = null): static
    {
        $this->userResolver = $callback;

        return $this;
    }

    public function getCreateSocialiteUserCallback(): Closure
    {
        return $this->createSocialiteUserCallback ?? fn (string $provider, SocialiteUserContract $oauthUser, Model $user) => SocialiteUser::create([
            'user_id' => $user->getKey(),
            'provider' => $provider,
            'provider_id' => $oauthUser->getId(),
        ]);
    }

    public function getCreateUserCallback(): Closure
    {
        return $this->createUserCallback ?? fn (SocialiteUserContract $oauthUser, FilamentSocialite $socialite) => $socialite->getUserModelClass()::create([
            'name' => $oauthUser->getName(),
            'email' => $oauthUser->getEmail(),
        ]);
    }

    public function getGuard(): StatefulGuard
    {
        return $this->auth->guard(
            $this->config->get('filament.auth.guard')
        );
    }

    public function isRegistrationEnabled(): bool
    {
        return $this->getConfig()['registration'] == true;
    }
}
