<?php

namespace Sfneal\Cruise\Utils;

trait ScriptsPath
{
    private function getVersionScriptPath(string $script): string
    {
        return $this->getScriptPath("version/$script");
    }

    private function getScriptPath(string $script_path): string
    {
        return base_path("vendor/sfneal/cruise/scripts/$script_path");
    }
}
