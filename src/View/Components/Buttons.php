<?php

namespace DutchCodingCompany\FilamentSocialite\View\Components;

use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Provider;
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
            'providers' => $providers = $this->plugin->getProviders(),
            'visibleProviders' => array_filter($providers, fn (Provider $provider) => $provider->isVisible()),
            'socialiteRoute' => $this->plugin->getRoute(),
            'messageBag' => $messageBag,
        ]);
    }
}
