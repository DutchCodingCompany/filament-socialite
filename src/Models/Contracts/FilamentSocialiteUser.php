<?php

namespace DutchCodingCompany\FilamentSocialite\Models\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;

interface FilamentSocialiteUser
{
    public function getUser(): Authenticatable;

    public static function findForProvider(string $provider, SocialiteUserContract $oauthUser): ?self;

    public static function createForProvider(string $provider, SocialiteUserContract $oauthUser, Authenticatable $user): self;
}
