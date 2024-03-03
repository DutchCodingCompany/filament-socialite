<?php

namespace DutchCodingCompany\FilamentSocialite\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \DutchCodingCompany\FilamentSocialite\FilamentSocialite
 */
class FilamentSocialite extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'filament-socialite';
    }
}
