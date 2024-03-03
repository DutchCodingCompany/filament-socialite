<?php

namespace DutchCodingCompany\FilamentSocialite\Models;

use DutchCodingCompany\FilamentSocialite\Facades\FilamentSocialite;
use DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser as FilamentSocialiteUserContract;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;

/**
 * @property int $user_id
 * @property string $provider
 * @property int $provider_id
 */
class SocialiteUser extends Model implements FilamentSocialiteUserContract
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Illuminate\Database\Eloquent\Model&\Illuminate\Contracts\Auth\Authenticatable, self>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(FilamentSocialite::getUserModelClass());
    }

    public function getUser(): Authenticatable
    {
        return $this->user;
    }

    public static function findForProvider(string $provider, SocialiteUserContract $oauthUser): ?self
    {
        return self::query()
            ->where('provider', $provider)
            ->where('provider_id', $oauthUser->getId())
            ->first();
    }

    public static function createForProvider(string $provider, SocialiteUserContract $oauthUser, Authenticatable $user): self
    {
        return self::query()
            ->create([
                'user_id' => $user->getKey(),
                'provider' => $provider,
                'provider_id' => $oauthUser->getId(),
            ]);
    }
}
