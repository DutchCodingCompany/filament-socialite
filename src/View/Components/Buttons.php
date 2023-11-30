<?php

namespace DutchCodingCompany\FilamentSocialite\View\Components;

use DutchCodingCompany\FilamentSocialite\FilamentSocialite;
use Illuminate\Support\MessageBag;
use Illuminate\View\Component;

class Buttons extends Component
{
    public function __construct(
        protected FilamentSocialite $socialite,
        public bool $showDivider = true,
    ) {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function render()
    {
        $messageBag = new MessageBag();

        if (session()->has('filament-socialite-login-error')) {
            $messageBag->add('login-failed', session()->pull('filament-socialite-login-error'));
        }

        return view('filament-socialite::components.buttons', [
            'providers' => $this->socialite->getPlugin()->getProviders(),
            'socialiteRoute' => $this->socialite->getPlugin()->getRoute(),
            'messageBag' => $messageBag,
        ]);
    }
}
