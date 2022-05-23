<?php

namespace DutchCodingCompany\FilamentSocialite;

use Illuminate\Contracts\Config\Repository;

class FilamentSocialite
{
    public function __construct(
        protected Repository $config
    ) {
    }

    public function getConfig(): array
    {
        return $this->config->get('filament-socialite', []);
    }

    public function getUserModelClass(): string
    {
        return $this->getConfig()['user_model'] ?? \App\Models\User::class;
    }
}
