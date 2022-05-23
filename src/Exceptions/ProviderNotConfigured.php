<?php

namespace DutchCodingCompany\FilamentSocialite\Exceptions;

use LogicException;

class ProviderNotConfigured extends LogicException
{
    public static function make(string $provider): static
    {
        return new static('Provider "'.$provider.'" is not configured.');
    }
}
