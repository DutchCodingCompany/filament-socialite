<?php

namespace DutchCodingCompany\FilamentSocialite\Tests;

use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\FilamentSocialiteServiceProvider;
use DutchCodingCompany\FilamentSocialite\Tests\Fixtures\TestUser;
use Filament\Facades\Filament;
use Filament\FilamentServiceProvider;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Encryption\Encrypter;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Schema;
use Laravel\Socialite\SocialiteServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use LazilyRefreshDatabase;
    use LazilyRefreshDatabase;

    protected string $panelName = 'testpanel';

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (
                string $modelName,
            ) => 'DutchCodingCompany\\FilamentSocialite\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        $this->app->make(Kernel::class)->pushMiddleware(StartSession::class);
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
                ->id($this->panelName)
                ->path($this->panelName)
                ->login()
                ->pages([
                    Dashboard::class,
                ])
                ->plugins([
                    FilamentSocialitePlugin::make()
                        ->setProviders([
                            'github' => [
                                'label' => 'GitHub',
                                'icon' => 'fab-github',
                                'color' => 'danger',
                                'outlined' => false,
                            ],
                            'gitlab' => [
                                'label' => 'GitLab',
                                'icon' => 'fab-gitlab',
                            ],
                        ])
                        ->setRegistrationEnabled(true)
                        ->setUserModelClass(TestUser::class),
                ]),
        );
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('mysql.driver', ':memory:');

        config()->set('app.key', 'base64:'.base64_encode(
            Encrypter::generateKey('AES-256-CBC')
        ));

        config()->set('services.github', [
            'client_id' => 'abcdmockedabcd',
            'client_secret' => 'defgmockeddefg',
            'redirect' => 'http://localhost/oauth/callback/github',
        ]);

        config()->set('database.default', 'testing');
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__.'/Fixtures');

        $this->artisan('migrate', ['--database' => 'testing'])->run();

        Schema::table('users', static function (Blueprint $table): void {
            $table->string('password')->nullable()->change();
        });
    }
}
