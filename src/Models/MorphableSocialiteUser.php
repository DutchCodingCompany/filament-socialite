<?php

namespace DutchCodingCompany\FilamentSocialite\Models;

use DutchCodingCompany\FilamentSocialite\Facades\FilamentSocialite;
use DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser as FilamentSocialiteUserContract;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;

class MorphableSocialiteUser extends Model implements FilamentSocialiteUserContract
{
    protected $table = 'socialite_users';

    protected $fillable = [
        'provider',
        'provider_id',
    ];

    public function user(): MorphTo
    {
        return $this->morphTo();
    }

    public function getUser(): Authenticatable
    {
        return $this->user;
    }

    public static function findForProvider(string $provider, SocialiteUserContract $oauthUser): ?self
    {
        $user = app()->call(FilamentSocialite::getUserResolver(), ['provider' => $provider, 'oauthUser' => $oauthUser]);

        return $user?->socialiteUsers()
            ->where('provider', $provider)
            ->where('provider_id', $oauthUser->getId())
            ->first();
    }

    public static function createForProvider(
        string $provider,
        SocialiteUserContract $oauthUser,
        Authenticatable $user,
    ): self {
        return $user->socialiteUsers()->updateOrCreate(
            ['provider' => $provider],
            ['provider_id' => $oauthUser->getId()]
        );
    }
}
