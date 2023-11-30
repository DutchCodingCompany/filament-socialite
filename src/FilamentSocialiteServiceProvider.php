<?php

namespace DutchCodingCompany\FilamentSocialite;

use DutchCodingCompany\FilamentSocialite\Http\Livewire\Buttons;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
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
        Livewire::component('filament-socialite.buttons', Buttons::class);

        FilamentView::registerRenderHook(
            'panels::auth.login.form.after',
            static function (): string {
                if (! filament()->getCurrentPanel()->hasPlugin('filament-socialite')) {
                    return '';
                }

                return Blade::render('@livewire(\'filament-socialite.buttons\')');
            },
        );
    }
}
