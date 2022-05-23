<?php

namespace DutchCodingCompany\FilamentSocialite\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;

class UserNotAllowed
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        public SocialiteUserContract $oauthUser,
    ) {
    }
}
