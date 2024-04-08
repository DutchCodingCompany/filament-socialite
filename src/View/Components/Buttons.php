<?php

namespace DutchCodingCompany\FilamentSocialite\View\Components;

use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use Illuminate\Support\MessageBag;
use Illuminate\View\Component;

class Buttons extends Component
{
    public function __construct(
        protected FilamentSocialitePlugin $plugin,
        public bool $showDivider = true,
    ) {
        //
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $messageBag = new MessageBag();

        if (session()->has('filament-socialite-login-error')) {
            $messageBag->add('login-failed', session()->pull('filament-socialite-login-error'));
        }

        return view('filament-socialite::components.buttons', [
            'providers' => $this->plugin->getProviders(),
            'socialiteRoute' => $this->plugin->getRoute(),
            'messageBag' => $messageBag,
        ]);
    }
}
