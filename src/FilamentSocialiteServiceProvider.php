<?php

namespace DutchCodingCompany\FilamentSocialite;

use DutchCodingCompany\FilamentSocialite\Http\Livewire\Buttons;
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
            ->hasTranslations()
            ->hasViews()
            ->hasRoute('web')
            ->hasMigration('create_socialite_users_table');
    }

    public function packageBooted(): void
    {
        Livewire::component('filament-socialite.buttons', Buttons::class);

        FilamentView::registerRenderHook(
            'panels::auth.login.form.after',
            static fn (): string => Blade::render('@livewire(\'filament-socialite.buttons\')'),
        );
    }
}
