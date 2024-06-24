<?php

namespace Sfneal\Cruise\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class Version extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'version
                            {--path= : The path to version.txt file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display the application version';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('v'.self::get($this->option('path')));

        return self::SUCCESS;
    }

    public static function get(?string $path = null): string
    {
        return file_get_contents($path ?? base_path('version.txt'));
    }
}
