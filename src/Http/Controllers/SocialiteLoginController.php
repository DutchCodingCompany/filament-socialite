<?php

namespace DutchCodingCompany\FilamentSocialite\Http\Controllers;

use DutchCodingCompany\FilamentSocialite\Events;
use DutchCodingCompany\FilamentSocialite\Exceptions\ProviderNotConfigured;
use DutchCodingCompany\FilamentSocialite\FilamentSocialite;
use DutchCodingCompany\FilamentSocialite\Models\SocialiteUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class SocialiteLoginController extends Controller
{
    public function __construct(
        protected FilamentSocialite $socialite,
    ) {
    }

    public function redirectToProvider(string $provider)
    {
        if (! $this->socialite->isProviderConfigured($provider)) {
            throw ProviderNotConfigured::make($provider);
        }

        return Socialite::with($provider)
            ->scopes($this->socialite->getProviderScopes($provider))
            ->redirect();
    }

    protected function retrieveOauthUser(string $provider): ?SocialiteUserContract
    {
        try {
            return Socialite::driver($provider)->user();
        } catch (InvalidStateException $e) {
            Events\InvalidState::dispatch($e);
        }

        return null;
    }

    protected function retrieveSocialiteUser(string $provider, SocialiteUserContract $user): ?SocialiteUser
    {
        return SocialiteUser::query()
            ->where('provider', $provider)
            ->where('provider_id', $user->getId())
            ->first();
    }

    protected function redirectToLogin(string $message): RedirectResponse
    {
        // Redirect back to the login route with an error message attached
        return redirect()->route(config('filament-socialite.login_page_route', 'filament.auth.login'))
                ->withErrors([
                    'email' => [
                        __($message),
                    ],
                ]);
    }

    protected function isUserAllowed(SocialiteUserContract $user): bool
    {
        $domains = $this->socialite->getDomainAllowList();

        // When no domains are specified, all users are allowed
        if (count($domains) < 1) {
            return true;
        }

        // Get the domain of the email for the specified user
        $emailDomain = Str::of($user->getEmail())
            ->afterLast('@')
            ->lower()
            ->__toString();

        // See if everything after @ is in the domains array
        if (in_array($emailDomain, $domains)) {
            return true;
        }

        return false;
    }

    protected function loginUser(SocialiteUser $socialiteUser)
    {
        // Log the user in
        $this->socialite->getGuard()->login($socialiteUser->user, config('filament-socialite.remember_login', false));

        // Dispatch the login event
        Events\Login::dispatch($socialiteUser);

        // Redirect as intended
        return redirect()->intended(
            route($this->socialite->getLoginRedirectRoute())
        );
    }

    protected function registerSocialiteUser(string $provider, SocialiteUserContract $oauthUser, Model $user)
    {
        // Create a socialite user
        $socialiteUser = app()->call($this->socialite->getCreateSocialiteUserCallback(), ['provider' => $provider, 'oauthUser' => $oauthUser, 'user' => $user, 'socialite' => $this->socialite]);

        // Dispatch the socialite user connected event
        Events\SocialiteUserConnected::dispatch($socialiteUser);

        // Login the user
        return $this->loginUser($socialiteUser);
    }

    protected function registerOauthUser(string $provider, SocialiteUserContract $oauthUser)
    {
        $socialiteUser = DB::transaction(function () use ($provider, $oauthUser) {
            // Create a user
            $user = app()->call($this->socialite->getCreateUserCallback(), ['provider' => $provider, 'oauthUser' => $oauthUser, 'socialite' => $this->socialite]);

            // Create a socialite user
            return app()->call($this->socialite->getCreateSocialiteUserCallback(), ['provider' => $provider, 'oauthUser' => $oauthUser, 'user' => $user, 'socialite' => $this->socialite]);
        });

        // Dispatch the registered event
        Events\Registered::dispatch($socialiteUser);

        // Login the user
        return $this->loginUser($socialiteUser);
    }

    public function processCallback(string $provider)
    {
        // See if provider exists
        if (! $this->socialite->isProviderConfigured($provider)) {
            throw ProviderNotConfigured::make($provider);
        }

        // Try to retrieve existing user
        $oauthUser = $this->retrieveOauthUser($provider);
        if (is_null($oauthUser)) {
            return $this->redirectToLogin('auth.login-failed');
        }

        // Verify if user is allowed
        if (! $this->isUserAllowed($oauthUser)) {
            Events\UserNotAllowed::dispatch($oauthUser);

            return $this->redirectToLogin('auth.user-not-allowed');
        }

        // Try to find a socialite user
        $socialiteUser = $this->retrieveSocialiteUser($provider, $oauthUser);
        if ($socialiteUser) {
            return $this->loginUser($socialiteUser);
        }

        // See if registration is allowed
        if (! $this->socialite->isRegistrationEnabled()) {
            Events\RegistrationNotEnabled::dispatch($provider, $oauthUser);

            return $this->redirectToLogin('auth.registration-not-enabled');
        }

        // See if a user already exists, but not for this socialite provider
        $user = app()->call($this->socialite->getUserResolver(), ['provider' => $provider, 'oauthUser' => $oauthUser, 'socialite' => $this->socialite]);

        // Handle registration
        return $user
            ? $this->registerSocialiteUser($provider, $oauthUser, $user)
            : $this->registerOauthUser($provider, $oauthUser);
    }
}
