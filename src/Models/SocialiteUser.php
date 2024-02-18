<?php

namespace DutchCodingCompany\FilamentSocialite\Models;

use DutchCodingCompany\FilamentSocialite\Facades\FilamentSocialite;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $user_id
 * @property string $provider
 * @property int $provider_id
 */
class SocialiteUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user',
        'provider',
        'provider_id',
    ];

    public function user(): MorphTo
    {
        return $this->morphTo();
    }
}
