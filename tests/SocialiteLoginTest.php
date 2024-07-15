<?php

namespace DutchCodingCompany\FilamentSocialite\Tests;

use Closure;
use DutchCodingCompany\FilamentSocialite\Events\InvalidState;
use DutchCodingCompany\FilamentSocialite\Events\RegistrationNotEnabled;
use DutchCodingCompany\FilamentSocialite\Events\UserNotAllowed;
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Tests\Fixtures\TestSocialiteUser;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Laravel\Socialite\Facades\Socialite;
use LogicException;
use PHPUnit\Framework\Attributes\DataProvider;

class SocialiteLoginTest extends TestCase
{
    #[DataProvider('loginDataProvider')]
    public function testLogin(
        string $email,
        string $callbackRoute,
        ?string $overrideState = null,
        ?string $dispatchedErrorEvent = null,
    ): void {
        Event::fake();

        $response = $this
            ->getJson(route("socialite.filament.{$this::getPanelName()}.oauth.redirect", ['provider' => 'github']))
            ->assertStatus(302);

        $state = session()->get('state');

        $location = $response->headers->get('location') ?? throw new LogicException('Location header not set.');

        parse_str($location, $urlQuery);

        // Test if the correct state is sent to the endpoint in the "Location" header.
        $this->assertEquals($state, $urlQuery['state']);

        // Assert decrypting of the state gives the correct panel name.
        $this->assertEquals($this::getPanelName(), Crypt::decrypt($state));

        $user = new TestSocialiteUser();
        $user->email = $email;

        Socialite::shouldReceive('driver')
            ->with('github')
            ->andReturn(static::makeOAuthProviderMock(
                request()->merge(['state' => $overrideState ?? $state]),
                $user
            ));

        // Fake oauth response.
        $response = $this
            ->getJson(route($callbackRoute, ['provider' => 'github', 'state' => $state]))
            ->assertStatus(302);

        if ($dispatchedErrorEvent) {
            Event::assertDispatched($dispatchedErrorEvent);

            $this->assertDatabaseMissing('socialite_users', [
                'provider' => 'github',
                'provider_id' => 'test-socialite-user-id',
            ]);

            $this->assertDatabaseMissing('users', [
                'name' => 'test-socialite-user-name',
                'email' => $user->email,
            ]);
        } else {
            $this->assertDatabaseHas('socialite_users', [
                'provider' => 'github',
                'provider_id' => 'test-socialite-user-id',
            ]);

            $this->assertDatabaseHas('users', [
                'name' => 'test-socialite-user-name',
                'email' => $user->email,
            ]);
        }
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: ?string, 3: ?string}>
     */
    public static function loginDataProvider(): array
    {
        return [
            'Login fails when incorrect state (panelized callback route)' => [
                'test@example.com',
                // Use the new callback route that already contains the panel in the url.
                'socialite.filament.'.static::getPanelName().'.oauth.callback',
                'invalid-mocked-state',
                InvalidState::class,
            ],
            'Login fails when incorrect state (general callback route)' => [
                'test@example.com',
                // Use the old callback route that determines the panel based on the state parameter.
                'oauth.callback',
                'invalid-mocked-state',
                InvalidState::class,
            ],
            'Login succeeds when email in domain allow list' => [
                'test@example.com',
                'socialite.filament.'.static::getPanelName().'.oauth.callback',
                null,
                null,
            ],
            'Login fails when email not in domain allow list' => [
                'test@example1.com',
                'socialite.filament.'.static::getPanelName().'.oauth.callback',
                null,
                UserNotAllowed::class,
            ],
        ];
    }

    #[DataProvider('registrationBlockProvider')]
    public function testRegistrationBlock(bool $createUser, Closure | bool $registrationEnabled): void
    {
        Event::fake();

        if ($createUser) {
            DB::table('users')->insert([
                'name' => 'test-user',
                'email' => 'test@example.com',
            ]);
        }

        FilamentSocialitePlugin::current()->registration($registrationEnabled);

        $this
            ->getJson(route("socialite.filament.{$this::getPanelName()}.oauth.redirect", ['provider' => 'github']))
            ->assertStatus(302);

        $state = session()->get('state');

        Socialite::shouldReceive('driver')
            ->with('github')
            ->andReturn(static::makeOAuthProviderMock(
                request()->merge(['state' => $state]),
                new TestSocialiteUser()
            ));

        // Fake oauth response.
        $this
            ->getJson(route("socialite.filament.{$this::getPanelName()}.oauth.callback", ['provider' => 'github', 'state' => $state]))
            ->assertStatus(302);

        if (! $createUser) {
            // If there is no user, the event should have been dispatched since the plugin option disabled registration.
            Event::assertDispatched(RegistrationNotEnabled::class);
        }
    }

    /**
     * @return array<string, array<mixed>>
     */
    public static function registrationBlockProvider(): array
    {
        $callback = function (string $provider, SocialiteUserContract $oauthUser, ?Authenticatable $user) {
            return (bool) $user;
        };

        return [
            'Authenticatable exists for socialite user' => [true, $callback],
            'Authenticatable does not exist for socialite user' => [false, $callback],
            'Registration is always blocked' => [true, false],
        ];
    }
}
