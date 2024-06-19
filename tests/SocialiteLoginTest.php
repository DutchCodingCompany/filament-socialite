<?php

namespace DutchCodingCompany\FilamentSocialite\Tests;

use Closure;
use DutchCodingCompany\FilamentSocialite\Events\RegistrationNotEnabled;
use DutchCodingCompany\FilamentSocialite\Events\UserNotAllowed;
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Tests\Fixtures\TestSocialiteUser;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Laravel\Socialite\Facades\Socialite;
use LogicException;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;

class SocialiteLoginTest extends TestCase
{
    #[DataProvider('loginDataProvider')]
    public function testLogin(string $email, bool $dispatchesUserNotAllowedEvent): void
    {
        Event::fake();

        $response = $this
            ->getJson("/$this->panelName/oauth/github")
            ->assertStatus(302);

        $state = session()->get('state');

        $location = $response->headers->get('location') ?? throw new LogicException('Location header not set.');

        parse_str($location, $urlQuery);

        // Test if the correct state is sent to the endpoint in the "Location" header.
        $this->assertEquals($state, $urlQuery['state']);

        // Assert decrypting of the state gives the correct panel name.
        $this->assertEquals($this->panelName, Crypt::decrypt($state));

        $user = new TestSocialiteUser();
        $user->email = $email;

        Socialite::shouldReceive('driver')
            ->with('github')
            ->andReturn(
                Mockery::mock(Provider::class)
                    ->shouldReceive('user')
                    ->andReturn($user)
                    ->getMock()
            );

        // Fake oauth response.
        $response = $this
            ->getJson("/oauth/callback/github?state=$state")
            ->assertStatus(302);

        if ($dispatchesUserNotAllowedEvent) {
            Event::assertDispatched(UserNotAllowed::class);
        } else {
            $this->assertDatabaseHas('socialite_users', [
                'provider' => 'github',
                'provider_id' => 'test-socialite-user-id',
            ]);

            $this->assertDatabaseHas('users', [
                'name' => 'test-socialite-user-name',
                'email' => 'test@example.com',
            ]);
        }
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
            ->getJson("/$this->panelName/oauth/github")
            ->assertStatus(302);

        Socialite::shouldReceive('driver')
            ->with('github')
            ->andReturn(
                Mockery::mock(Provider::class)
                    ->shouldReceive('user')
                    ->andReturn(new TestSocialiteUser())
                    ->getMock()
            );

        $state = session()->get('state');

        // Fake oauth response.
        $this
            ->getJson("/oauth/callback/github?state=$state")
            ->assertStatus(302);

        if (! $createUser) {
            // If there is no user, the event should have been dispatched since the plugin option disabled registration.
            Event::assertDispatched(RegistrationNotEnabled::class);
        }
    }

    /**
     * @return array<string, array{0: string, 1: bool}>
     */
    public static function loginDataProvider(): array
    {
        return [
            'Email with domain in domain allow list is allowed to login' => [
                'test@example.com',
                false,
            ],
            'Email with domain not in domain allow list is not allowed to login' => [
                'test@example1.com',
                true,
            ],
        ];
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
