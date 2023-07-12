<?php

declare(strict_types=1);

namespace Medas\FileSystemTest\Functional;

use Medas\FileSystem\PathNormalizer;
use PHPUnit\Framework\TestCase;

class PathNormalizerTest extends TestCase
{
    public function testBasics(): void
    {
        $currentDirPlusVarPlusTest = __DIR__ . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'test';

        self::assertEquals(
            $currentDirPlusVarPlusTest,
            $this->normalize('var/test')
        );

        self::assertEquals(
            $currentDirPlusVarPlusTest,
            $this->normalize('./var/test')
        );

        self::assertEquals(
            $currentDirPlusVarPlusTest,
            $this->normalize('higher/../var/test')
        );

        self::assertEquals(
            $currentDirPlusVarPlusTest,
            $this->normalize('var//test')
        );

        self::assertEquals(
            $currentDirPlusVarPlusTest,
            $this->normalize('var/./test')
        );

        self::assertEquals(
            '/var/log/test',
            $this->normalize('/var/log/test')
        );

        self::assertEquals(
            '\\\\var\\log\\test',
            $this->normalize('\\\\var\\log\\test')
        );
    }

    private function normalize(
        string $path,
        string $directorySeparator = DIRECTORY_SEPARATOR,
        string $workingDirectory = null,
    ): string
    {
        $workingDirectory ??= __DIR__;
        return service(PathNormalizer::class)->normalize($path, $directorySeparator, $workingDirectory);
    }
}
