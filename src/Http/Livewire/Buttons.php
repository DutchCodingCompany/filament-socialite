<?php

namespace DutchCodingCompany\FilamentSocialite\Http\Livewire;

use Filament\Facades\Filament;
use Illuminate\Support\MessageBag;
use Livewire\Component;

class Buttons extends Component
{
    public function render()
    {
        $messageBag = new MessageBag();
        if (session()->has('filament-socialite-login-error')) {
            $messageBag->add('login-failed', session()->pull('filament-socialite-login-error'));
        }

        return view('filament-socialite::livewire.buttons', [
            'providers' => Filament::getCurrentPanel()
                ->getPlugin('filament-socialite')
                ->getProviders(),
            'messageBag' => $messageBag,
        ]);
    }
}
