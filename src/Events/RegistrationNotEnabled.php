<?php

namespace DutchCodingCompany\FilamentSocialite\Events;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;

class RegistrationNotEnabled
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        public string $provider,
        public SocialiteUserContract $oauthUser,
        public ?Authenticatable $user,
    ) {
    }
}
