<?php

namespace DutchCodingCompany\FilamentSocialite\Exceptions;

use LogicException;

class GuardNotStateful extends LogicException
{
    public static function make(string $guard): static
    {
        return new static('Guard "'.$guard.'" is not stateful.');
    }
}
