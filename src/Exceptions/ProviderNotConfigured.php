<?php

namespace DutchCodingCompany\FilamentSocialite\Exceptions;

use LogicException;

class ProviderNotConfigured extends LogicException
{
    public static function make(string $provider): self
    {
        return new self('Provider "'.$provider.'" is not configured, please configure it in config/services.php and/or on your panel.');
    }
}
