<?php

namespace DutchCodingCompany\FilamentSocialite\Traits;

use Closure;
use DutchCodingCompany\FilamentSocialite\Exceptions\ImplementationException;
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser as FilamentSocialiteUserContract;
use Filament\Facades\Filament;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;

trait Callbacks
{
    /**
     * @phpstan-var ?\Closure(string $provider, \Laravel\Socialite\Contracts\User $oauthUser, self $socialite): \Illuminate\Contracts\Auth\Authenticatable
     */
    protected ?Closure $createUserUsing = null;

    /**
     * @phpstan-var ?\Closure(string $provider, \DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser $socialiteUser, \DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin $plugin): \Symfony\Component\HttpFoundation\Response
     */
    protected ?Closure $redirectAfterLoginUsing = null;

    /**
     * @phpstan-var ?\Closure(string $provider, \Laravel\Socialite\Contracts\User $oauthUser, \DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin $plugin): ?(\Illuminate\Contracts\Auth\Authenticatable)
     */
    protected ?Closure $resolveUserUsing = null;

    /**
     * @phpstan-var ?\Closure(\DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin $plugin, \Laravel\Socialite\Contracts\User $oauthUser): bool
     */
    protected ?Closure $authorizeUserUsing = null;

    /**
     * @param ?\Closure(string $provider, \Laravel\Socialite\Contracts\User $oauthUser, \DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin $plugin): \Illuminate\Contracts\Auth\Authenticatable $callback
     */
    public function createUserUsing(?Closure $callback = null): static
    {
        $this->createUserUsing = $callback;

        return $this;
    }

    /**
     * @return \Closure(string $provider, \Laravel\Socialite\Contracts\User $oauthUser, \DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin $plugin): \Illuminate\Contracts\Auth\Authenticatable
     */
    public function getCreateUserUsing(): Closure
    {
        return $this->createUserUsing ?? function (
            string $provider,
            SocialiteUserContract $oauthUser,
            FilamentSocialitePlugin $plugin,
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

    /**
     * @param \Closure(string $provider, \DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser $socialiteUser, \DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin $plugin): \Illuminate\Http\RedirectResponse $callback
     */
    public function redirectAfterLoginUsing(Closure $callback): static
    {
        $this->redirectAfterLoginUsing = $callback;

        return $this;
    }

    /**
     * @return \Closure(string $provider, \DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser $socialiteUser, \DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin $plugin): \Symfony\Component\HttpFoundation\Response
     */
    public function getRedirectAfterLoginUsing(): Closure
    {
        return $this->redirectAfterLoginUsing ?? function (string $provider, FilamentSocialiteUserContract $socialiteUser, FilamentSocialitePlugin $plugin) {
            if (($panel = $this->getPanel())->hasTenancy()) {
                $tenant = Filament::getUserDefaultTenant($socialiteUser->getUser());

                if (is_null($tenant) && $tenantRegistrationUrl = $panel->getTenantRegistrationUrl()) {
                    return redirect()->intended($tenantRegistrationUrl);
                }

                return redirect()->intended(
                    $panel->getUrl($tenant)
                );
            }

            return redirect()->intended(
                $this->getPanel()->getUrl()
            );
        };
    }

    /**
     * @param ?\Closure(string $provider, \Laravel\Socialite\Contracts\User $oauthUser, \DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin $plugin): ?(\Illuminate\Contracts\Auth\Authenticatable) $callback
     */
    public function resolveUserUsing(?Closure $callback = null): static
    {
        $this->resolveUserUsing = $callback;

        return $this;
    }

    /**
     * @return \Closure(string $provider, \Laravel\Socialite\Contracts\User $oauthUser, \DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin $plugin): ?(\Illuminate\Contracts\Auth\Authenticatable)
     */
    public function getResolveUserUsing(): Closure
    {
        return $this->resolveUserUsing ?? function (string $provider, SocialiteUserContract $oauthUser, FilamentSocialitePlugin $plugin) {
            /** @var \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model&\Illuminate\Contracts\Auth\Authenticatable> $model */
            $model = (new $this->userModelClass());

            return $model->where(
                'email',
                $oauthUser->getEmail()
            )->first();
        };
    }

    /**
     * @param ?\Closure(\DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin $plugin, \Laravel\Socialite\Contracts\User $oauthUser): bool $callback
     */
    public function authorizeUserUsing(?Closure $callback = null): static
    {
        $this->authorizeUserUsing = $callback;

        return $this;
    }

    /**
     * @return \Closure(\DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin $plugin, \Laravel\Socialite\Contracts\User $oauthUser): bool
     */
    public function getAuthorizeUserUsing(): Closure
    {
        return $this->authorizeUserUsing ?? static::checkDomainAllowList(...);
    }

    public static function checkDomainAllowList(FilamentSocialitePlugin $plugin, SocialiteUserContract $oauthUser): bool
    {
        $domains = $plugin->getDomainAllowList();

        // When no domains are specified, all users are allowed
        if (count($domains) < 1) {
            return true;
        }

        // Get the domain of the email for the specified user
        $emailDomain = Str::of($oauthUser->getEmail() ?? throw new ImplementationException('User email is required.'))
            ->afterLast('@')
            ->lower()
            ->__toString();

        // See if everything after @ is in the domains array
        return in_array($emailDomain, $domains);
    }
}
