<?php

namespace InfinityXTech\FilamentApiDocsBuilder\Commands;

use Illuminate\Console\Command;

class FilamentApiDocsBuilderCommand extends Command
{
    public $signature = 'filament-api-docs-builder';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
