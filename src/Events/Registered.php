<?php

namespace DutchCodingCompany\FilamentSocialite\Events;

use DutchCodingCompany\FilamentSocialite\Models\SocialiteUser;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Registered
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        public SocialiteUser $socialiteUser,
    ) {
    }
}
