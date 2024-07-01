<?php

namespace DutchCodingCompany\FilamentSocialite\Tests;

use Closure;
use DutchCodingCompany\FilamentSocialite\Events\InvalidState;
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Provider;
use DutchCodingCompany\FilamentSocialite\Tests\Fixtures\TestSocialiteUser;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Event;
use Laravel\Socialite\Facades\Socialite;
use LogicException;
use PHPUnit\Framework\Attributes\DataProvider;

class SocialiteStatelessLoginTest extends TestCase
{
    protected function registerTestPanel(): void
    {
        Filament::registerPanel(
            fn (): Panel => Panel::make()
                ->default()
                ->id($this::getPanelName())
                ->path($this::getPanelName())
                ->tenant(...$this->tenantArguments)
                ->login()
                ->pages([
                    Dashboard::class,
                ])
                ->plugins([
                    FilamentSocialitePlugin::make()
                        ->providers([
                            Provider::make('github')
                                ->label('GitHub')
                                ->icon('fab-github')
                                ->color('danger')
                                ->outlined(false)
                                ->stateless(),
                            Provider::make('gitlab')
                                ->label('GitLab')
                                ->icon('fab-gitlab')
                                ->color('danger')
                                ->outlined()
                                ->scopes([])
                                ->with([]),
                        ])
                        ->registration(true)
                        ->userModelClass($this->userModelClass)
                        ->domainAllowList(['example.com']),
                ]),
        );
    }

    #[DataProvider('statelessLoginDataProvider')]
    public function testStatelessLogin(
        string $email,
        string $callbackRoute,
        ?string $overrideState = null,
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

        Event::assertNotDispatched(InvalidState::class);

        $this->assertDatabaseHas('socialite_users', [
            'provider' => 'github',
            'provider_id' => 'test-socialite-user-id',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'test-socialite-user-name',
            'email' => $user->email,
        ]);
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: ?string, 3: ?string}>
     */
    public static function statelessLoginDataProvider(): array
    {
        return [
            'Stateless login succeeds (panelized callback route)' => [
                'test@example.com',
                // Use the new callback route that already contains the panel in the url.
                'socialite.filament.'.static::getPanelName().'.oauth.callback',
                null,
                InvalidState::class,
            ],
            'Stateless login succeeds with mocked state (general callback route)' => [
                'test@example.com',
                // Use the old callback route that determines the panel based on the state parameter.
                'oauth.callback',
                'invalid-mocked-state',
                InvalidState::class,
            ],
        ];
    }
}
