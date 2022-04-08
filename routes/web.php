<?php

Route::domain(config('filament.domain'))
    ->middleware(config('filament.middleware.base'))
    ->name('socialite.')
    ->group(function () {
        Route::get('/oauth/{provider}', [
            \DutchCodingCompany\FilamentSocialite\Http\Controllers\SocialiteLoginController::class,
            'redirectToProvider',
        ])
            ->name('oauth.redirect');

        Route::get('/oauth/callback/{provider}', [
            \DutchCodingCompany\FilamentSocialite\Http\Controllers\SocialiteLoginController::class,
            'processCallback',
        ])
            ->name('oauth.callback');
    });
