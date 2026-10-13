<?php

namespace DutchCodingCompany\FilamentSocialite\Events;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\SerializesModels;

class OauthClientError
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
        public ClientException | RequestException $exception,
    ) {
    }
}
