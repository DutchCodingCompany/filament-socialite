<?php

namespace DutchCodingCompany\FilamentSocialite\Http\Livewire;

use DutchCodingCompany\FilamentSocialite\FilamentSocialite;
use Illuminate\Support\MessageBag;
use Livewire\Component;

class Buttons extends Component
{
    protected FilamentSocialite $socialite;

    public function boot(FilamentSocialite $socialite)
    {
        $this->socialite = $socialite;
    }

    public function render()
    {
        $messageBag = new MessageBag();
        if (session()->has('filament-socialite-login-error')) {
            $messageBag->add('login-failed', session()->pull('filament-socialite-login-error'));
        }

        return view('filament-socialite::livewire.buttons', [
            'providers' => $this->socialite->getPlugin()->getProviders(),
            'socialiteRoute' => $this->socialite->getPlugin()->getRoute(),
            'messageBag' => $messageBag,
        ]);
    }
}
