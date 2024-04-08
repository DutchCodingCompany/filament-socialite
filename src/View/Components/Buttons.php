<?php

namespace DutchCodingCompany\FilamentSocialite\View\Components;

use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use Illuminate\Support\MessageBag;
use Illuminate\View\Component;

class Buttons extends Component
{
    protected FilamentSocialitePlugin $plugin;

    public function __construct(
        public bool $showDivider = true,
    ) {
        $this->plugin = FilamentSocialitePlugin::current();
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
