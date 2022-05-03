<?php

namespace DutchCodingCompany\FilamentSocialite;

use DutchCodingCompany\FilamentSocialite\Http\Livewire\Buttons;
use Filament\PluginServiceProvider;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;

class FilamentSocialiteServiceProvider extends PluginServiceProvider
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

    public function packageBooted(): void
    {
        Livewire::component('filament-socialite.buttons', Buttons::class);
    }
}
