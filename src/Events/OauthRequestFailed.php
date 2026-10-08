<?php

namespace DutchCodingCompany\FilamentSocialite\Events;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Queue\SerializesModels;

class OauthRequestFailed
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
        public GuzzleException | HttpClientException $exception,
    ) {
    }
}
