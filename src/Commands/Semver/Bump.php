<?php

namespace Sfneal\Cruise\Commands\Semver;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\Process;
use function Laravel\Prompts\search;

class Bump extends Command implements PromptsForMissingInput
{
    const TYPES = ['major', 'minor', 'patch'];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bump
                            {type : major, minor or patch version bump}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bump the application to the next major, minor or patch version';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $process = Process::path(base_path())->run(['bash', $this->getScriptPath(), '--' . $this->argument('bump')]);

        $this->info($process->output());

        return $process->exitCode();
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array<string, string>
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'user' => fn () => search(
                label: 'Which semver segment would you like to bump?',
                options: fn ($type) => self::TYPES,
                placeholder: 'E.g. major, minor or patch',
            ),
        ];
    }

    private function getScriptPath(): string
    {
        $rel_path = 'scripts/version/bump.sh';
        if (file_exists($rel_path)) {
            return $rel_path;
        }

        return base_path('vendor/sfneal/cruise/' . $rel_path);
    }
}
