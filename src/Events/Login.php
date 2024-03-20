<?php

namespace DutchCodingCompany\FilamentSocialite\Events;

use DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser as FilamentSocialiteUserContract;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class Login
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        public FilamentSocialiteUserContract $socialiteUser,
        public SocialiteUser $oauthUser,
    ) {
    }
}
