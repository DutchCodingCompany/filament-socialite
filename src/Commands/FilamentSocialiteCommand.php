<?php

namespace DutchCodingCompany\FilamentSocialite\Commands;

use Illuminate\Console\Command;

class FilamentSocialiteCommand extends Command
{
    public $signature = 'filament-socialite';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
