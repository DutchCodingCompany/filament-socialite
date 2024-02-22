<?php

namespace DutchCodingCompany\FilamentSocialite\Models\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

interface FilamentSocialiteUser
{
    public function getUser(): Model&Authenticatable;
}
