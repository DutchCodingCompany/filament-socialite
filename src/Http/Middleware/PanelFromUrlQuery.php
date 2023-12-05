<?php

namespace DutchCodingCompany\FilamentSocialite\Http\Middleware;

use Closure;
use DutchCodingCompany\FilamentSocialite\Exceptions\InvalidCallbackPayload;
use Filament\Http\Middleware\SetUpPanel;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

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
            return Crypt::decrypt($request->query('state'));
        } catch (DecryptException $e) {
            throw InvalidCallbackPayload::make($e);
        }
    }
}
