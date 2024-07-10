<?php

namespace DutchCodingCompany\FilamentSocialite;

use DutchCodingCompany\FilamentSocialite\View\Components\Buttons;
use Filament\Facades\Filament;
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

        FilamentView::registerRenderHook(
            'panels::auth.login.form.after',
            static function (): ?string {
                $panel = Filament::getCurrentPanel();

                if (! $panel?->hasPlugin('filament-socialite')) {
                    return null;
                }

                /** @var \DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin $plugin */
                $plugin = $panel->getPlugin('filament-socialite');

                return Blade::render('<x-filament-socialite::buttons :show-divider="'.($plugin->getShowDivider() ? 'true' : 'false').'" />');
            },
        );

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
