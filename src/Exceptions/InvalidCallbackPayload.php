<?php

namespace DutchCodingCompany\FilamentSocialite\Exceptions;

use LogicException;
use Throwable;

class InvalidCallbackPayload extends LogicException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct('The panel could not be decrypted from the OAuth callback.', 0, $previous);
    }

    public static function make(): self
    {
        return new self(...func_get_args());
    }
}
