<?php

namespace DutchCodingCompany\FilamentSocialite\View\Components;

use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Provider;
use Illuminate\Support\MessageBag;
use Illuminate\View\Component;

class Divider extends Component
{
    public function __construct() {
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $messageBag = new MessageBag();

        return view('filament-socialite::components.divider', [
            'messageBag' => $messageBag,
        ]);
    }
}
