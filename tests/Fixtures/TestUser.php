<?php

namespace DutchCodingCompany\FilamentSocialite\Tests\Fixtures;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class TestUser extends Model implements Authenticatable
{
    protected $table = 'users';

    protected $guarded = [];

    public function getAuthIdentifierName()
    {
        //
    }

    public function getAuthIdentifier()
    {
        //
    }

    public function getAuthPassword()
    {
        //
    }

    public function getRememberToken()
    {
        //
    }

    public function setRememberToken($value)
    {
        //
    }

    public function getRememberTokenName()
    {
        //
    }
}
