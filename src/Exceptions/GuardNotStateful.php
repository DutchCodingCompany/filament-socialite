<?php

namespace DutchCodingCompany\FilamentSocialite\Exceptions;

use LogicException;

class GuardNotStateful extends LogicException
{
    public static function make(string $guard): self
    {
        return new self('Guard "'.$guard.'" is not stateful.');
    }
}
