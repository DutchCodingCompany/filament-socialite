<?php

namespace DutchCodingCompany\FilamentSocialite\Models\Concerns;

use DutchCodingCompany\FilamentSocialite\Models\MorphableSocialiteUser;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Class MorphableSocialite
 *
 * This class is a trait that provides functionality for handling morphable socialite users.
 */
trait MorphableSocialite
{
    protected static function bootMorphableSocialite()
    {
        self::deleting(function ($model) {
            $model->socialiteUsers()->delete();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\DutchCodingCompany\FilamentSocialite\Models\SocialiteUser>
     */
    public function socialiteUsers(): MorphMany
    {
        return $this->morphMany(MorphableSocialiteUser::class, 'user');
    }
}
