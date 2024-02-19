<?php

namespace DutchCodingCompany\FilamentSocialite\Models\Concerns;

use DutchCodingCompany\FilamentSocialite\Models\SocialiteUser;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait FilamentSocialite
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\DutchCodingCompany\FilamentSocialite\Models\SocialiteUser>
     */
    public function socialiteUsers(): MorphMany
    {
        return $this->morphMany(SocialiteUser::class, 'authenticatable');
    }
}
