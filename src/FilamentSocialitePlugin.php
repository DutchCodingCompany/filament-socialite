<?php

namespace DutchCodingCompany\FilamentSocialite;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentSocialitePlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-socialite';
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        return filament(static::make()->getId());
    }
}
