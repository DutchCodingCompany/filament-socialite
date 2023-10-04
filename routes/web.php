<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

Route::domain(Filament::getCurrentPanel()->getDomains()[0] ?? '')
    ->middleware(Filament::getCurrentPanel()->getMiddleware())
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
