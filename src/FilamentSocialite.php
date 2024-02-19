<?php

namespace DutchCodingCompany\FilamentSocialite;

use Closure;
use DutchCodingCompany\FilamentSocialite\Exceptions\GuardNotStateful;
use DutchCodingCompany\FilamentSocialite\Exceptions\ProviderNotConfigured;
use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Eloquent\Model;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialite as FilamentSocialiteContract;

class FilamentSocialite
{
    protected ?Closure $userResolver = null;

    protected ?Closure $createSocialiteUserCallback = null;

    protected ?Closure $createUserCallback = null;

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
        /** @var FilamentSocialitePlugin */
        return Filament::getCurrentPanel()->getPlugin('filament-socialite');
    }

    public function isProviderConfigured(string $provider): bool
    {
        return $this->config->has('services.' . $provider);
    }

    public function getProviderConfig(string $provider): array
    {
        if (!$this->isProviderConfigured($provider)) {
            throw ProviderNotConfigured::make($provider);
        }

        return $this->config->get('services.' . $provider);
    }

    public function getProviderScopes(string $provider): string | array
    {
        return $this->getProviderConfig($provider)['scopes'] ?? [];
    }

    public function getOptionalParameters(string $provider): array
    {
        return $this->getProviderConfig($provider)['with'] ?? [];
    }

    /**
     * @return class-string<Model>
     */
    public function getUserModelClass(): string
    {
        return $this->getPlugin()->getUserModelClass();
    }

    public function getUserModel(): Model
    {
        return new ($this->getUserModelClass());
    }

    /**
     * @return \Closure(): \Illuminate\Contracts\Auth\Authenticatable
     */
    public function getUserResolver(): Closure
    {
        return $this->userResolver ?? fn (SocialiteUserContract $oauthUser) => $this->getUserModel()->where(
            'email',
            $oauthUser->getEmail()
        )->first();
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

    /**
     * @return \Closure(): SocialiteUserContract
     */
    public function getCreateSocialiteUserCallback(): Closure
    {
        return $this->createSocialiteUserCallback ?? fn (
            string $provider,
            SocialiteUserContract $oauthUser,
            FilamentSocialiteContract $user
        ) => $user->socialiteUsers()->updateOrCreate(
            ['provider' => $provider],
            ['provider_id' => $oauthUser->getId()]
        );
    }

    /**
     * @return \Closure(): \Illuminate\Contracts\Auth\Authenticatable
     */
    public function getCreateUserCallback(): Closure
    {
        return $this->createUserCallback ?? fn (
            SocialiteUserContract $oauthUser,
            FilamentSocialite $socialite
        ) => $socialite->getUserModelClass()::create([
            'name' => $oauthUser->getName(),
            'email' => $oauthUser->getEmail(),
        ]);
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
}
