<?php

namespace DutchCodingCompany\FilamentSocialite;

use DutchCodingCompany\FilamentSocialite\Http\Livewire\Buttons;
use Filament\Support\Facades\FilamentView;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;

class FilamentSocialiteServiceProvider extends PackageServiceProvider
{
    public function packageRegistered(): void
    {
        $this->app->singleton(FilamentSocialite::class);
        $this->app->alias(FilamentSocialite::class, 'filament-socialite');

        parent::packageRegistered();
    }

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

    public function packageBooted(): void
    {
        Livewire::component('filament-socialite.buttons', Buttons::class);
        Blade::componentNamespace('DutchCodingCompany\\FilamentSocialite\\View\\Components', 'filament-socialite');

        FilamentView::registerRenderHook(
            'panels::auth.login.form.after',
            static fn (): string => Blade::render('@livewire(\'filament-socialite.buttons\')'),
        );
    }
}
