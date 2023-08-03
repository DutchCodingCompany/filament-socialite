<?php

namespace DutchCodingCompany\FilamentSocialite\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \DutchCodingCompany\FilamentSocialite\FilamentSocialite
 */
class FilamentSocialite extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \DutchCodingCompany\FilamentSocialite\FilamentSocialite::class;
    }
}
