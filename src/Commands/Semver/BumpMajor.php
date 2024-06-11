<?php

namespace Sfneal\Cruise\Commands\Semver;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class BumpMajor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bump:major';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bump the application to the next major version (e.g. v1.0 to v2.0)';

    /**
     * Execute the console command.
     *
     *
     * @throws Exception
     */
    public function handle(): int
    {
        return Artisan::call('bump major');
    }
}
