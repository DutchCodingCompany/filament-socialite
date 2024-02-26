<?php

namespace DutchCodingCompany\FilamentSocialite\Http\Controllers;

use DutchCodingCompany\FilamentSocialite\Events;
use DutchCodingCompany\FilamentSocialite\Exceptions\ProviderNotConfigured;
use DutchCodingCompany\FilamentSocialite\FilamentSocialite;
use DutchCodingCompany\FilamentSocialite\Http\Middleware\PanelFromUrlQuery;
use DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser as FilamentSocialiteUserContract;
use Illuminate\Contracts\Auth\Authenticatable;
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
        //
    }

    public function redirectToProvider(string $provider): RedirectResponse
    {
        if (! $this->socialite->isProviderConfigured($provider)) {
            throw ProviderNotConfigured::make($provider);
        }

        $redirect = Socialite::driver($provider)
            ->with([
                ...$this->socialite->getOptionalParameters($provider),
                'state' => $state = PanelFromUrlQuery::encrypt($this->socialite->getPanelId()),
            ])
            ->scopes($this->socialite->getProviderScopes($provider))
            ->redirect();

        // Set state value to be equal to the encrypted panel id. This value is used to
        // retrieve the panel id once the authentication returns to our application,
        // and it still prevents CSRF as it is non-guessable value.
        session()->put('state', $state);

        return $redirect;
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

    protected function retrieveSocialiteUser(string $provider, SocialiteUserContract $user): ?FilamentSocialiteUserContract
    {
        /** @var ?\DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser $model */
        $model = $this->socialite->getSocialiteUserModelClass()::query()
            ->where('provider', $provider)
            ->where('provider_id', $user->getId())
            ->first();

        return $model;
    }

    protected function redirectToLogin(string $message): RedirectResponse
    {
        // Add error message to the session, this way we can show an error message on the form.
        session()->flash('filament-socialite-login-error', __($message));

        return redirect()->route($this->socialite->getPlugin()->getLoginRouteName());
    }

    protected function isUserAllowed(SocialiteUserContract $user): bool
    {
        $domains = $this->socialite->getPlugin()->getDomainAllowList();

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

    protected function loginUser(FilamentSocialiteUserContract $socialiteUser): RedirectResponse
    {
        // Log the user in
        $this->socialite->getGuard()->login($socialiteUser->getUser(), $this->socialite->getPlugin()->getRememberLogin());

        // Dispatch the login event
        Events\Login::dispatch($socialiteUser);

        // Redirect as intended
        return redirect()->intended(
            route($this->socialite->getPlugin()->getDashboardRouteName())
        );
    }

    protected function registerSocialiteUser(string $provider, SocialiteUserContract $oauthUser, Authenticatable $user): RedirectResponse
    {
        // Create a socialite user
        $socialiteUser = app()->call($this->socialite->getCreateSocialiteUserCallback(), ['provider' => $provider, 'oauthUser' => $oauthUser, 'user' => $user, 'socialite' => $this->socialite]);

        // Dispatch the socialite user connected event
        Events\SocialiteUserConnected::dispatch($socialiteUser);

        // Login the user
        return $this->loginUser($socialiteUser);
    }

    protected function registerOauthUser(string $provider, SocialiteUserContract $oauthUser): RedirectResponse
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

    public function processCallback(string $provider): RedirectResponse
    {
        if (! $this->socialite->isProviderConfigured($provider)) {
            throw ProviderNotConfigured::make($provider);
        }

        // Try to retrieve existing user
        $oauthUser = $this->retrieveOauthUser($provider);

        if (is_null($oauthUser)) {
            return $this->redirectToLogin('filament-socialite::auth.login-failed');
        }

        // Verify if user is allowed
        if (! $this->isUserAllowed($oauthUser)) {
            Events\UserNotAllowed::dispatch($oauthUser);

            return $this->redirectToLogin('filament-socialite::auth.user-not-allowed');
        }

        // Try to find a socialite user
        $socialiteUser = $this->retrieveSocialiteUser($provider, $oauthUser);
        if ($socialiteUser) {
            return $this->loginUser($socialiteUser);
        }

        // See if registration is allowed
        if (! $this->socialite->getPlugin()->getRegistrationEnabled()) {
            Events\RegistrationNotEnabled::dispatch($provider, $oauthUser);

            return $this->redirectToLogin('filament-socialite::auth.registration-not-enabled');
        }

        // See if a user already exists, but not for this socialite provider
        $user = app()->call($this->socialite->getUserResolver(), ['provider' => $provider, 'oauthUser' => $oauthUser, 'socialite' => $this->socialite]);

        // Handle registration
        return $user
            ? $this->registerSocialiteUser($provider, $oauthUser, $user)
            : $this->registerOauthUser($provider, $oauthUser);
    }
}
