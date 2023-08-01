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
}
