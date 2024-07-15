<?php

use DutchCodingCompany\FilamentSocialite\Http\Controllers\SocialiteLoginController;
use DutchCodingCompany\FilamentSocialite\Http\Middleware\PanelFromUrlQuery;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

foreach (Filament::getPanels() as $panel) {
    if (! $panel->hasPlugin('filament-socialite')) {
        continue;
    }

    // Retrieve slug for route name.
    $slug = $panel->getPlugin('filament-socialite')->getSlug();

    $domains = $panel->getDomains();

    foreach ((empty($domains) ? [null] : $domains) as $domain) {
        Filament::currentDomain($domain);

        Route::domain($domain)
            ->middleware($panel->getMiddleware())
            ->name("socialite.{$panel->generateRouteName('oauth.redirect')}")
            ->get("/$slug/oauth/{provider}", [SocialiteLoginController::class, 'redirectToProvider']);

        Route::domain($domain)
            ->match(['get', 'post'], "$slug/oauth/callback/{provider}", [SocialiteLoginController::class, 'processCallback'])
            ->middleware([
                ...$panel->getMiddleware(),
                ...config('filament-socialite.middleware'),
            ])
            ->name("socialite.{$panel->generateRouteName('oauth.callback')}");

        Filament::currentDomain(null);
    }
}

/**
 * @note This route can only distinguish between Filament panels using the `state` input. If you have a stateless OAuth
 * implementation, use the "$slug/oauth/callback/{provider}" route instead which has the panel in the URL itself.
 */
Route::match(['get', 'post'], "/oauth/callback/{provider}", [SocialiteLoginController::class, 'processCallback'])
    ->middleware([
        PanelFromUrlQuery::class,
        ...config('filament-socialite.middleware'),
    ])
    ->name('oauth.callback');
