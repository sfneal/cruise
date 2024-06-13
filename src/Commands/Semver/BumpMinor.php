<?php

namespace Sfneal\Cruise\Commands\Semver;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class BumpMinor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bump:minor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bump the application to the next minor version (e.g. v1.1 to v1.2)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        return Artisan::call('bump minor');
    }
}
