<?php

use DutchCodingCompany\FilamentSocialite\Http\Controllers\SocialiteLoginController;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

$panel = Filament::getDefaultPanel();

Route::domain($panel->getDomain())
    ->middleware($panel->getMiddleWare())
    ->name('socialite.')
    ->group(function () {
        Route::get('/oauth/{provider}', [
            SocialiteLoginController::class,
            'redirectToProvider',
        ])
            ->name('oauth.redirect');

        Route::get('/oauth/callback/{provider}', [
            SocialiteLoginController::class,
            'processCallback',
        ])
            ->name('oauth.callback');
    });
