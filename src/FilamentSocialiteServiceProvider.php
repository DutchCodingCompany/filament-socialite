<?php

namespace DutchCodingCompany\FilamentSocialite;

use DutchCodingCompany\FilamentSocialite\View\Components\Buttons;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
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
        $this->app->singleton(FilamentSocialite::class);
        $this->app->alias(FilamentSocialite::class, 'filament-socialite');
    }

    public function packageBooted(): void
    {
        Blade::componentNamespace('DutchCodingCompany\FilamentSocialite\View\Components', 'filament-socialite');
        Blade::component('buttons', Buttons::class);

        FilamentView::registerRenderHook(
            'panels::auth.login.form.after',
            static function (): ?string {
                if (! ($panel = Filament::getCurrentPanel())->hasPlugin('filament-socialite')) {
                    return null;
                }

                /** @var \DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin $plugin */
                $plugin = $panel->getPlugin('filament-socialite');

                return Blade::render('<x-filament-socialite::buttons :show-divider="'.($plugin->getShowDivider() ? 'true' : 'false').'" />');
            },
        );
    }
}
