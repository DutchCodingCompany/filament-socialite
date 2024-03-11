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
        return 'test-user-auth-identifier-name';
    }

    public function getAuthIdentifier()
    {
        return 'test-user-auth-identifier';
    }

    public function getAuthPassword()
    {
        return 'test-user-auth-password';
    }

    public function getAuthPasswordName()
    {
        return 'test-user-auth-password';
    }

    public function getRememberToken()
    {
        return 'test-user-remember-token';
    }

    public function setRememberToken($value)
    {
        //
    }

    public function getRememberTokenName()
    {
        return 'test-user-remember-token-name';
    }
}
