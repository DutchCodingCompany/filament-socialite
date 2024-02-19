<?php

namespace DutchCodingCompany\FilamentSocialite\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface FilamentSocialite
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\DutchCodingCompany\FilamentSocialite\Models\SocialiteUser>
     */
    public function socialiteUsers(): MorphMany;
}
