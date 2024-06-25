<?php

namespace Sfneal\Cruise\Services;

use Illuminate\Support\Facades\Process;
use Sfneal\Cruise\Utils\ScriptsPath;

class Semver
{
    use ScriptsPath;

    private string $version;

    public function __construct(string $version)
    {
        $this->version = $version;
    }

    public static function for(string $version): Semver
    {
        return new static($version);
    }

    public function major(): string
    {
        return $this->runSemver('major');
    }

    public function minor(): string
    {
        return $this->runSemver('minor');
    }

    public function patch(): string
    {
        return $this->runSemver('patch');
    }

    private function runSemver(string $type): string
    {
        return trim(Process::path(base_path())
            ->run([
                $this->getVersionScriptPath('semver'),
                'bump',
                $type,
                $this->version,
            ])
            ->output());
    }
}
