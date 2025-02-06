<?php

namespace DutchCodingCompany\FilamentSocialite\Events;

use DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser as FilamentSocialiteUserContract;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;

class SocialiteUserConnected
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
        public FilamentSocialiteUserContract $socialiteUser,
    ) {
    }
}
