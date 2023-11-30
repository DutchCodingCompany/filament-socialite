<?php

namespace DutchCodingCompany\FilamentSocialite\Exceptions;

use LogicException;

class ProviderNotConfigured extends LogicException
{
    public static function make(string $provider): ProviderNotConfigured
    {
        return new ProviderNotConfigured('Provider "'.$provider.'" is not configured, please configure it in config/services.php.');
    }
}
