<?php

namespace DutchCodingCompany\FilamentSocialite;

use DutchCodingCompany\FilamentSocialite\Http\Livewire\Buttons;
use Filament\PluginServiceProvider;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;

class FilamentSocialiteServiceProvider extends PluginServiceProvider
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
    }
}
