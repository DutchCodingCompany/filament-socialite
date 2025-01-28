<?php

namespace DutchCodingCompany\FilamentSocialite\Http\Controllers;

use DutchCodingCompany\FilamentSocialite\Events;
use DutchCodingCompany\FilamentSocialite\Exceptions\ProviderNotConfigured;
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Http\Middleware\PanelFromUrlQuery;
use DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser as FilamentSocialiteUserContract;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Symfony\Component\HttpFoundation\Response;

class SocialiteLoginController extends Controller
{
    use EvaluatesClosures;

    private ?FilamentSocialitePlugin $plugin = null;

    public function redirectToProvider(string $provider): mixed
    {
        if (! $this->plugin()->isProviderConfigured($provider)) {
            throw ProviderNotConfigured::make($provider);
        }

        /** @var \Laravel\Socialite\Two\AbstractProvider $driver */
        $driver = Socialite::driver($provider);

        $response = $driver
            ->with([
                ...$this->plugin()->getProvider($provider)->getWith(),
                'state' => $state = PanelFromUrlQuery::encrypt($this->plugin()->getPanel()->getId()),
            ])
            ->scopes($this->plugin()->getProvider($provider)->getScopes())
            ->redirect();

        // Set state value to be equal to the encrypted panel id. This value is used to
        // retrieve the panel id once the authentication returns to our application,
        // and it still prevents CSRF as it is non-guessable value.
        session()->put('state', $state);

        return $response;
    }

    protected function retrieveOauthUser(string $provider): ?SocialiteUserContract
    {
        $stateless = $this->plugin()->getProvider($provider)->getStateless();

        try {
            /** @var \Laravel\Socialite\Two\AbstractProvider $driver */
            $driver = Socialite::driver($provider);

            return $stateless
                ? $driver->stateless()->user()
                : $driver->user();
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

    protected function authorizeUser(SocialiteUserContract $oauthUser): bool
    {
        return app()->call($this->plugin()->getAuthorizeUserUsing(), ['plugin' => $this->plugin(), 'oauthUser' => $oauthUser]);
    }

    protected function loginUser(string $provider, FilamentSocialiteUserContract $socialiteUser, SocialiteUserContract $oauthUser): Response
    {
        // Log the user in
        $this->plugin()->getGuard()->login($socialiteUser->getUser(), $this->plugin()->getRememberLogin());

        // Dispatch the login event
        Events\Login::dispatch($socialiteUser, $oauthUser);

        return app()->call($this->plugin()->getRedirectAfterLoginUsing(), ['provider' => $provider, 'socialiteUser' => $socialiteUser, 'plugin' => $this->plugin]);
    }

    protected function registerSocialiteUser(string $provider, SocialiteUserContract $oauthUser, Authenticatable $user): Response
    {
        // Create a socialite user
        $socialiteUser = $this->plugin()->getSocialiteUserModel()::createForProvider($provider, $oauthUser, $user);

        // Dispatch the socialite user connected event
        Events\SocialiteUserConnected::dispatch($provider, $oauthUser, $socialiteUser);

        // Login the user
        return $this->loginUser($provider, $socialiteUser, $oauthUser);
    }

    protected function registerOauthUser(string $provider, SocialiteUserContract $oauthUser): Response
    {
        $socialiteUser = DB::transaction(function () use ($provider, $oauthUser) {
            // Create a user
            $user = app()->call($this->plugin()->getCreateUserUsing(), ['provider' => $provider, 'oauthUser' => $oauthUser, 'plugin' => $this->plugin]);

            // Create a socialite user
            return $this->plugin()->getSocialiteUserModel()::createForProvider($provider, $oauthUser, $user);
        });

        // Dispatch the registered event
        Events\Registered::dispatch($provider, $oauthUser, $socialiteUser);

        // Login the user
        return $this->loginUser($provider, $socialiteUser, $oauthUser);
    }

    public function processCallback(string $provider): Response
    {
        if (! $this->plugin()->isProviderConfigured($provider)) {
            throw ProviderNotConfigured::make($provider);
        }

        // Try to retrieve existing user
        $oauthUser = $this->retrieveOauthUser($provider);

        if (is_null($oauthUser)) {
            return $this->redirectToLogin('filament-socialite::auth.login-failed');
        }

        // Verify if the user is authorized.
        if (! $this->authorizeUser($oauthUser)) {
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
