<?php

namespace DutchCodingCompany\FilamentSocialite\Models;

use DutchCodingCompany\FilamentSocialite\Facades\FilamentSocialite;
use DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser as FilamentSocialiteUserContract;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function getUser(): Model & Authenticatable
    {
        return $this->user;
    }
}
