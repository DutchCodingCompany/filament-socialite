<?php

namespace DutchCodingCompany\FilamentSocialite\View\Components;

use DutchCodingCompany\FilamentSocialite\Facades\FilamentSocialite;
use Illuminate\View\Component;

class Buttons extends Component
{
    /**
     * The alert type.
     *
     * @var string
     */
    public $providers;

    /**
     * Create the component instance.
     *
     * @param  string  $type
     * @param  string  $message
     * @return void
     */
    public function __construct(?array $providers = null)
    {
        $this->providers = $providers ?? FilamentSocialite::getProviderButtons();
    }

    /**
     * {@inheritDoc}
     */
    public function render()
    {
        return view('filament-socialite::components.buttons');
    }
}
