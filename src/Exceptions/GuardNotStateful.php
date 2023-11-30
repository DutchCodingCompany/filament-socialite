<?php

namespace DutchCodingCompany\FilamentSocialite\Exceptions;

use LogicException;

class GuardNotStateful extends LogicException
{
    public static function make(string $guard): GuardNotStateful
    {
        return new GuardNotStateful('Guard "'.$guard.'" is not stateful.');
    }
}
