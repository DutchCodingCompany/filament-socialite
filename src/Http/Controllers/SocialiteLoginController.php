<?php

namespace DutchCodingCompany\FilamentSocialite\Http\Controllers;

use DutchCodingCompany\FilamentSocialite\Events;
use DutchCodingCompany\FilamentSocialite\Exceptions\ImplementationException;
use DutchCodingCompany\FilamentSocialite\Exceptions\ProviderNotConfigured;
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Http\Middleware\PanelFromUrlQuery;
use DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser as FilamentSocialiteUserContract;
use Filament\Support\Concerns\EvaluatesClosures;
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
    use EvaluatesClosures;

    private ?FilamentSocialitePlugin $plugin = null;

    public function redirectToProvider(string $provider): RedirectResponse
    {
        if (! $this->plugin()->isProviderConfigured($provider)) {
            throw ProviderNotConfigured::make($provider);
        }

        /** @var \Laravel\Socialite\Two\AbstractProvider $driver */
        $driver = Socialite::driver($provider);

        $redirect = $driver
            ->with([
                ...$this->plugin()->getOptionalParameters($provider),
                'state' => $state = PanelFromUrlQuery::encrypt($this->plugin()->getPanel()->getId()),
            ])
            ->scopes($this->plugin()->getProviderScopes($provider))
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

    protected function retrieveSocialiteUser(string $provider, SocialiteUserContract $oauthUser): ?FilamentSocialiteUserContract
    {
        return $this->plugin()->getSocialiteUserModel()::findForProvider($provider, $oauthUser);
    }

    protected function redirectToLogin(string $message): RedirectResponse
    {
        // Add error message to the session, this way we can show an error message on the form.
        session()->flash('filament-socialite-login-error', __($message));

        return redirect()->route($this->plugin()->getLoginRouteName());
    }

    protected function isUserAllowed(SocialiteUserContract $user): bool
    {
        $domains = $this->plugin()->getDomainAllowList();

        // When no domains are specified, all users are allowed
        if (count($domains) < 1) {
            return true;
        }

        // Get the domain of the email for the specified user
        $emailDomain = Str::of($user->getEmail() ?? throw new ImplementationException('User email is required.'))
            ->afterLast('@')
            ->lower()
            ->__toString();

        // See if everything after @ is in the domains array
        return in_array($emailDomain, $domains);
    }

    protected function loginUser(string $provider, FilamentSocialiteUserContract $socialiteUser, SocialiteUserContract $oauthUser): RedirectResponse
    {
        // Log the user in
        $this->plugin()->getGuard()->login($socialiteUser->getUser(), $this->plugin()->getRememberLogin());

        // Dispatch the login event
        Events\Login::dispatch($socialiteUser, $oauthUser);

        return app()->call($this->plugin()->getRedirectAfterLoginUsing(), ['provider' => $provider, 'socialiteUser' => $socialiteUser, 'plugin' => $this->plugin]);
    }

    protected function registerSocialiteUser(string $provider, SocialiteUserContract $oauthUser, Authenticatable $user): RedirectResponse
    {
        // Create a socialite user
        $socialiteUser = $this->plugin()->getSocialiteUserModel()::createForProvider($provider, $oauthUser, $user);

        // Dispatch the socialite user connected event
        Events\SocialiteUserConnected::dispatch($socialiteUser);

        // Login the user
        return $this->loginUser($provider, $socialiteUser, $oauthUser);
    }

    protected function registerOauthUser(string $provider, SocialiteUserContract $oauthUser): RedirectResponse
    {
        $socialiteUser = DB::transaction(function () use ($provider, $oauthUser) {
            // Create a user
            $user = app()->call($this->plugin()->getCreateUserUsing(), ['provider' => $provider, 'oauthUser' => $oauthUser, 'plugin' => $this->plugin]);

            // Create a socialite user
            return $this->plugin()->getSocialiteUserModel()::createForProvider($provider, $oauthUser, $user);
        });

        // Dispatch the registered event
        Events\Registered::dispatch($socialiteUser);

        // Login the user
        return $this->loginUser($provider, $socialiteUser, $oauthUser);
    }

    public function processCallback(string $provider): RedirectResponse
    {
        if (! $this->plugin()->isProviderConfigured($provider)) {
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
            return $this->loginUser($provider, $socialiteUser, $oauthUser);
        }

        // See if a user already exists, but not for this socialite provider
        $user = app()->call($this->plugin()->getResolveUserUsing(), [
            'provider' => $provider,
            'oauthUser' => $oauthUser,
            'plugin' => $this->plugin,
        ]);

        // See if registration is allowed
        if (! $this->evaluate($this->plugin()->getRegistration(), ['provider' => $provider, 'oauthUser' => $oauthUser, 'user' => $user])) {
            Events\RegistrationNotEnabled::dispatch($provider, $oauthUser, $user);

            return $this->redirectToLogin('filament-socialite::auth.registration-not-enabled');
        }

        // Handle registration
        return $user
            ? $this->registerSocialiteUser($provider, $oauthUser, $user)
            : $this->registerOauthUser($provider, $oauthUser);
    }

    protected function plugin(): FilamentSocialitePlugin
    {
        return $this->plugin ??= FilamentSocialitePlugin::current();
    }
}
