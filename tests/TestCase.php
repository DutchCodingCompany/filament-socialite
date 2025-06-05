<?php

namespace DutchCodingCompany\FilamentSocialite\Tests;

use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\FilamentSocialiteServiceProvider;
use DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser as FilamentSocialiteUserContract;
use DutchCodingCompany\FilamentSocialite\Models\SocialiteUser;
use DutchCodingCompany\FilamentSocialite\Provider;
use DutchCodingCompany\FilamentSocialite\Tests\Fixtures\TestUser;
use Filament\Facades\Filament;
use Filament\FilamentServiceProvider;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Laravel\Socialite\SocialiteServiceProvider;
use Laravel\Socialite\Two\AbstractProvider;
use Livewire\LivewireServiceProvider;
use Mockery\LegacyMockInterface;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    /**
     * @var class-string<\Illuminate\Contracts\Auth\Authenticatable>
     */
    protected string $userModelClass = TestUser::class;

    /**
     * @var array{0: ?class-string<\Illuminate\Database\Eloquent\Model>, 1?: ?string, 2?: ?string}
     */
    protected array $tenantArguments = [null];

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(self::getPanelName());

        Factory::guessFactoryNamesUsing(
            fn (
                string $modelName,
            ) => 'DutchCodingCompany\\FilamentSocialite\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        $this->app?->make(Kernel::class)->pushMiddleware(StartSession::class);
    }

    protected function getPackageProviders($app)
    {
        $this->registerTestPanel();

        return [
            FilamentServiceProvider::class,
            FilamentSocialiteServiceProvider::class,
            SocialiteServiceProvider::class,
            LivewireServiceProvider::class,
        ];
    }

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
                                ->outlined(false),
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

    public function getEnvironmentSetUp($app)
    {
        config()->set('app.key', 'base64:'.base64_encode(
            Encrypter::generateKey('AES-256-CBC')
        ));

        config()->set('services.github', [
            'client_id' => 'abcdmockedabcd',
            'client_secret' => 'defgmockeddefg',
            'redirect' => 'http://localhost/oauth/callback/github',
        ]);
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__.'/Fixtures');
    }

    protected static function getPanelName(): string
    {
        return 'testpanel';
    }

    protected static function makeOAuthProviderMock(
        Request $request,
        SocialiteUserContract $user,
    ): LegacyMockInterface {
        $mock = \Mockery::mock(
            AbstractProvider::class,
            [$request, 'test-client-id', 'test-client-secret', 'test-redirect-url']
        )
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $mock->shouldReceive('getAccessTokenResponse')->andReturn([]);
        $mock->shouldReceive('getUserByToken')->andReturn([]);
        $mock->shouldReceive('userInstance')->andReturn($user);

        return $mock;
    }
}
