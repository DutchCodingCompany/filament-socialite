<?php

namespace DutchCodingCompany\FilamentSocialite\View\Components;

use DutchCodingCompany\FilamentSocialite\Facades\FilamentSocialite;
use Illuminate\View\Component;

class Buttons extends Component
{
    /**
     * The providers available.
     *
     * @var array
     */
    public $providers;

    /**
     * Create the component instance.
     *
     * @param  null|array  $providers
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
