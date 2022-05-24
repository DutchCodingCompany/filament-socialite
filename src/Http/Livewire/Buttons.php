<?php

namespace DutchCodingCompany\FilamentSocialite\Http\Livewire;

use DutchCodingCompany\FilamentSocialite\Facades\FilamentSocialite;
use Livewire\Component;

class Buttons extends Component
{
    public function render()
    {
        return view('filament-socialite::livewire.buttons', [
            'providers' => FilamentSocialite::getProviderButtons(),
        ]);
    }
}
