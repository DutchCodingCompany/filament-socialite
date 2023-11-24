<?php

use DutchCodingCompany\FilamentSocialite\Http\Controllers\SocialiteLoginController;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

foreach (Filament::getPanels() as $panel) {
    if (! $panel->hasPlugin('filament-socialite')) {
        continue;
    }

    // Retrieve slug for route name.
    $slug = $panel->getPlugin('filament-socialite')->getSlug();

    // Build domain options if they exist.
    $domains = ! empty($panel->getDomains()) ? '('.collect($panel->getDomains())->join('|').')' : null;

    Route::domain($domains)
        ->middleware($panel->getMiddleware())
        ->name("socialite.$slug.")
        ->group(function () use ($slug) {
            Route::get("/$slug/oauth/{provider}", [SocialiteLoginController::class, 'redirectToProvider'])
                ->name('oauth.redirect');

            Route::get("/$slug/oauth/callback/{provider}", [SocialiteLoginController::class, 'processCallback'])
                ->name('oauth.callback');
        })->where(['domain' => $domains]);
}

