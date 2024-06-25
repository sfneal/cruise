<?php

namespace Sfneal\Cruise\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Sfneal\Cruise\Services\Semver;
use Sfneal\Cruise\Tests\ScriptTestCase;
use Sfneal\Cruise\Tests\TestCase;

class SemverTest extends ScriptTestCase
{
    protected bool $shouldInstall = false;
    protected bool $shouldUninstall = false;

    public static function majorVersionsProvider(): array
    {
        return [
            '0.1.0 --> 1.0.0' => [
                'original' => '0.1.0',
                'expected' => '1.0.0'
            ],
            '0.8.2 --> 1.0.0' => [
                'original' => '0.8.2',
                'expected' => '1.0.0'
            ],
            '1.5.9 --> 2.0.0' => [
                'original' => '1.5.9',
                'expected' => '2.0.0'
            ],
            '7.4.0 --> 8.0.0' => [
                'original' => '7.4.0',
                'expected' => '8.0.0'
            ],
        ];
    }

    public static function minorVersionsProvider(): array
    {
        return [
            '0.1.0 --> 0.2.0' => [
                'original' => '0.1.0',
                'expected' => '0.2.0'
            ],
            '0.8.2 --> 0.9.0' => [
                'original' => '0.8.2',
                'expected' => '0.9.0'
            ],
            '1.5.9 --> 1.6.0' => [
                'original' => '1.5.9',
                'expected' => '1.6.0'
            ],
            '7.4.0 --> 7.5.0' => [
                'original' => '7.4.0',
                'expected' => '7.5.0'
            ],
        ];
    }

    public static function patchVersionsProvider(): array
    {
        return [
            '0.1.0 --> 0.1.1' => [
                'original' => '0.1.0',
                'expected' => '0.1.1'
            ],
            '0.8.2 --> 0.8.3' => [
                'original' => '0.8.2',
                'expected' => '0.8.3'
            ],
            '1.5.9 --> 1.5.10' => [
                'original' => '1.5.9',
                'expected' => '1.5.10'
            ],
            '7.4.0 --> 7.4.1' => [
                'original' => '7.4.0',
                'expected' => '7.4.1'
            ],
        ];
    }

    #[Test]
    #[DataProvider('majorVersionsProvider')]
    public function can_bump_major_version(string $original, string $expected)
    {
        $bump = Semver::for($original)->major();

        $this->assertIsString($bump);
        $this->assertEquals($expected, $bump);
    }

    #[Test]
    #[DataProvider('minorVersionsProvider')]
    public function can_bump_minor_version(string $original, string $expected)
    {
        $bump = Semver::for($original)->minor();

        $this->assertIsString($bump);
        $this->assertEquals($expected, $bump);
    }

    #[Test]
    #[DataProvider('patchVersionsProvider')]
    public function can_bump_patch(string $original, string $expected)
    {
        $bump = Semver::for($original)->patch();

        $this->assertIsString($bump);
        $this->assertEquals($expected, $bump);
    }
}
