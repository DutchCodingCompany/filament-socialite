<?php

namespace DutchCodingCompany\FilamentSocialite\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \DutchCodingCompany\FilamentSocialite\FilamentSocialite
 * @method static string getUserModelClass()
 */
class FilamentSocialite extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'filament-socialite';
    }
}
