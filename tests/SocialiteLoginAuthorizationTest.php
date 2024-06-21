<?php

namespace DutchCodingCompany\FilamentSocialite\Tests;

use DutchCodingCompany\FilamentSocialite\Events\UserNotAllowed;
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Provider as PluginProvider;
use DutchCodingCompany\FilamentSocialite\Tests\Fixtures\TestSocialiteUser;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Event;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Laravel\Socialite\Facades\Socialite;
use LogicException;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;

class SocialiteLoginAuthorizationTest extends TestCase
{
    protected function registerTestPanel(): void
    {
        Filament::registerPanel(
            fn (): Panel => Panel::make()
                ->default()
                ->id($this->panelName)
                ->path($this->panelName)
                ->tenant(...$this->tenantArguments)
                ->login()
                ->pages([
                    Dashboard::class,
                ])
                ->plugins([
                    FilamentSocialitePlugin::make()
                        ->providers([
                            PluginProvider::make('github')
                                ->label('GitHub')
                                ->icon('fab-github')
                                ->color('danger')
                                ->outlined(false),
                            PluginProvider::make('gitlab')
                                ->label('GitLab')
                                ->icon('fab-gitlab')
                                ->color('danger')
                                ->outlined()
                                ->scopes([])
                                ->with([]),
                        ])
                        ->registration(true)
                        ->userModelClass($this->userModelClass)
                        ->authorizeUserUsing(function (FilamentSocialitePlugin $plugin, SocialiteUserContract $oauthUser) {
                            return $oauthUser->getEmail() === 'test@example.com';
                        }),
                ]),
        );
    }

    #[DataProvider('loginDataProvider')]
    public function testAuthorizationLogin(string $email, bool $dispatchesUserNotAllowedEvent): void
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

        $dispatchesUserNotAllowedEvent
            ? Event::assertDispatched(UserNotAllowed::class)
            : Event::assertNotDispatched(UserNotAllowed::class);
    }

    /**
     * @return array<string, array{0: string, 1: bool}>
     */
    public static function loginDataProvider(): array
    {
        return [
            'User is authorized to use the application so UserNotAllowedEvent should not be dispatched' => [
                'test@example.com',
                false,
            ],
            'User is not authorized to use the application so UserNotAllowedEvent should be dispatched' => [
                'test@example1.com',
                true,
            ],
        ];
    }
}
