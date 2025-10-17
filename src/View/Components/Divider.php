<?php

namespace DutchCodingCompany\FilamentSocialite\View\Components;

use Illuminate\Support\MessageBag;
use Illuminate\View\Component;

class Divider extends Component
{
    public function __construct()
    {
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $messageBag = new MessageBag();

        /** @phpstan-var view-string $viewName */
        $viewName = 'filament-socialite::components.divider';
        return view($viewName, [
            'messageBag' => $messageBag,
        ]);
    }
}
