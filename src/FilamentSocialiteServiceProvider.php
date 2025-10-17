<?php

namespace DutchCodingCompany\FilamentSocialite;

use DutchCodingCompany\FilamentSocialite\View\Components\Buttons;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentSocialiteServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-socialite')
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews()
            ->hasRoute('web')
            ->hasMigration('create_socialite_users_table');
    }

    public function packageRegistered(): void
    {
        //
    }

    public function packageBooted(): void
    {
        Blade::componentNamespace('DutchCodingCompany\FilamentSocialite\View\Components', 'filament-socialite');
        Blade::component('buttons', Buttons::class);

        FilamentAsset::register([
            Css::make('filament-socialite-styles', __DIR__.'/../resources/dist/plugin.css')->loadedOnRequest(),
        ], package: 'filament-socialite');

        collect(Filament::getPanels())->each(function (Panel $panel) {

            if (!$panel->hasPlugin('filament-socialite')) {
                return;
            }

            /** @var \DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin $plugin */
            $plugin = $panel->getPlugin('filament-socialite');
            $hook = 'panels::auth.login.form.after';
            $showButtonsBeforeLogin = 'false';
            if ($plugin->getButtonsBeforeLogin()) {
                $hook = 'panels::auth.login.form.before';
                $showButtonsBeforeLogin = 'true';
            }

            FilamentView::registerRenderHook(
                $hook,
                function () use ($plugin, $showButtonsBeforeLogin): string {
                    return Blade::render(
                        '<x-filament-socialite::buttons :show-divider="'.($plugin->getShowDivider() ? 'true' : 'false').'" :show-buttons-before-login="'.$showButtonsBeforeLogin.'"/>'
                    );
                }
            );
        });

        if (
            version_compare(app()->version(), '11.0', '>=')
            && method_exists(VerifyCsrfToken::class, 'except')
        ) {
            VerifyCsrfToken::except([
                '*/oauth/callback/*',
                'oauth/callback/*',
            ]);
        }
    }
}
