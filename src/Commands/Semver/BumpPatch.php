<?php

namespace Sfneal\Cruise\Commands\Semver;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class BumpPatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bump:patch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bump the application to the next patch version (e.g. v1.3.4 to v1.3.5)';

    /**
     * Execute the console command.
     *
     *
     * @throws Exception
     */
    public function handle(): int
    {
        return Artisan::call('bump patch');
    }
}
