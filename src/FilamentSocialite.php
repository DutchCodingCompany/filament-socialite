<?php

namespace DutchCodingCompany\FilamentSocialite;

use DutchCodingCompany\FilamentSocialite\Exceptions\GuardNotStateful;
use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Config\Repository;

class FilamentSocialite
{
    protected FilamentSocialitePlugin $plugin;

    public function __construct(
        protected Repository $config,
        protected Factory $auth,
    ) {
        //
    }

    public function getPanelId(): string
    {
        return Filament::getCurrentPanel()->getId();
    }

    public function getPlugin(): FilamentSocialitePlugin
    {
        /** @var \DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin */
        return Filament::getCurrentPanel()->getPlugin('filament-socialite');
    }

    public function getGuard(): StatefulGuard
    {
        $guard = $this->auth->guard(
            $guardName = Filament::getCurrentPanel()->getAuthGuard()
        );

        if ($guard instanceof StatefulGuard) {
            return $guard;
        }

        throw GuardNotStateful::make($guardName);
    }
}
