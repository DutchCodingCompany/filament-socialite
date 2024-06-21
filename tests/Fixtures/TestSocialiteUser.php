<?php

namespace DutchCodingCompany\FilamentSocialite\Tests\Fixtures;

use Laravel\Socialite\Contracts\User;

class TestSocialiteUser implements User
{
    public string $email = 'test@example.com';

    public function getId()
    {
        return 'test-socialite-user-id';
    }

    public function getNickname()
    {
        return 'test-socialite-user-nickname';
    }

    public function getName()
    {
        return 'test-socialite-user-name';
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getAvatar()
    {
        return null;
    }
}
