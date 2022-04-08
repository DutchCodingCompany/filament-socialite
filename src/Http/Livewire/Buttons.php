<?php

namespace DutchCodingCompany\FilamentSocialite\Http\Livewire;

use Livewire\Component;

class Buttons extends Component
{
    public function render()
    {
        $providers = config('filament-socialite.providers');

        return view('filament-socialite::livewire.buttons', [
            'providers' => $providers
        ]);
    }
}
