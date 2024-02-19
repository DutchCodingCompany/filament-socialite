<?php

namespace DutchCodingCompany\FilamentSocialite\Models;

use DutchCodingCompany\FilamentSocialite\Facades\FilamentSocialite;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string $authenticatable_type
 * @property int $authenticatable_id
 * @property string $provider
 * @property int $provider_id
 * @property \Illuminate\Contracts\Auth\Authenticatable $authenticatable
 */
class SocialiteUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'provider_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<\Illuminate\Contracts\Auth\Authenticatable>
     */
    public function authenticatable(): MorphTo
    {
        return $this->morphTo();
    }
}
