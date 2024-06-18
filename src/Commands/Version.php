<?php

namespace Sfneal\Cruise\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\Process;
use function Laravel\Prompts\select;

class Version extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'version';

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
        $process = Process::path(base_path())->run("head -n 1 version.txt");

        $this->info('v' . $process->output());

        return $process->exitCode();
    }
}
