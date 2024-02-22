<?php

declare(strict_types=1);

namespace Medas\FileSystemTest\Functional;

use Medas\FileSystem\{DirectoryCreator, FileFinder};
use PHPUnit\Framework\TestCase;

class DirectoryManagerTest extends TestCase
{
    public function testFindByExtension(): void
    {
        $fileFinder = service(FileFinder::class);
        $files = iterator_to_array($fileFinder->findByExtension(__DIR__, 'php'));

        $this->assertContains(__FILE__, $files);
    }

    public function testCreateDirectory(): void
    {
        $directoryCreator = service(DirectoryCreator::class);
        $path = __DIR__ . '/test-directory';

        if (file_exists($path)) {
            rmdir($path);
        }

        $directoryCreator->create($path);

        self::assertDirectoryExists($path);

        if (file_exists($path)) {
            rmdir($path);
        }
    }
}
