<?php

namespace DutchCodingCompany\FilamentSocialite\Models;

use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser as FilamentSocialiteUserContract;
use Illuminate\Contracts\Auth\Authenticatable;
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
    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Illuminate\Database\Eloquent\Model, $this>
     */
    public function user(): BelongsTo
    {
        /** @var class-string<\Illuminate\Database\Eloquent\Model&\Illuminate\Contracts\Auth\Authenticatable> */
        $user = FilamentSocialitePlugin::current()->getUserModelClass();

        return $this->belongsTo($user);
    }

    public function getUser(): Authenticatable
    {
        assert($this->user instanceof Authenticatable);

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
