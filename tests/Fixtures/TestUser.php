<?php

namespace DutchCodingCompany\FilamentSocialite\Tests\Fixtures;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class TestUser extends Model implements Authenticatable
{
    protected $table = 'users';

    protected $guarded = [];

    public function getAuthIdentifierName(): string
    {
        return 'test-user-auth-identifier-name';
    }

    public function getAuthIdentifier(): string
    {
        return 'test-user-auth-identifier';
    }

    public function getAuthPassword(): string
    {
        return 'test-user-auth-password';
    }

    public function getAuthPasswordName(): string
    {
        return 'test-user-auth-password';
    }

    public function getRememberToken(): string
    {
        return 'test-user-remember-token';
    }

    public function setRememberToken($value): void
    {
        //
    }

    public function getRememberTokenName(): string
    {
        return 'test-user-remember-token-name';
    }
}
