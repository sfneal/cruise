<?php

namespace Sfneal\Cruise\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Sfneal\Cruise\Tests\TestCase;

class ConfigTest extends TestCase
{
    #[Test]
    public function config_has_bump_auto_commit()
    {
        $output = config('cruise.bump.auto-commit');

        $this->assertIsBool($output);
        $this->assertFalse($output);
    }
}
