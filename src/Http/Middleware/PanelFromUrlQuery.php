<?php

namespace DutchCodingCompany\FilamentSocialite\Http\Middleware;

use Closure;
use DutchCodingCompany\FilamentSocialite\Exceptions\InvalidCallbackPayload;
use Filament\Http\Middleware\SetUpPanel;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

/**
 * @note This callback uses the `state` input to determine the correct panel ID. A simpler
 * implementation is to use the "$slug/oauth/callback/{provider}" route instead, which
 * contains the panel ID in the url itself.
 */
class PanelFromUrlQuery
{
    public function handle(Request $request, Closure $next): mixed
    {
        // Set the current panel based on the encrypted panel name in the url query.
        return (new SetUpPanel())->handle($request, $next, static::decrypt($request));
    }

    public static function encrypt(string $panel): string
    {
        return Crypt::encrypt($panel);
    }

    /**
     * @throws InvalidCallbackPayload
     */
    public static function decrypt(Request $request): string
    {
        try {
            if (! is_string($request->get('state'))) {
                throw new DecryptException('State is not a string.');
            }

            return Crypt::decrypt($request->get('state'));
        } catch (DecryptException $e) {
            throw InvalidCallbackPayload::make($e);
        }
    }
}
