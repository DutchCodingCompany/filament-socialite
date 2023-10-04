<?php

namespace DutchCodingCompany\FilamentSocialite\Http\Livewire;

use Filament\Facades\Filament;
use Livewire\Component;

class Buttons extends Component
{
    public function render()
    {
        return view('filament-socialite::livewire.buttons', [
            'providers' => Filament::getCurrentPanel()
                ->getPlugin('filament-socialite')
                ->getProviders(),
        ]);
    }
}
