<?php

declare(strict_types=1);

namespace Medas\FileSystemTest\Functional;

use Medas\FileSystem\DirectoryManager;
use PHPUnit\Framework\TestCase;

class DirectoryManagerTest extends TestCase
{
    public function testRecursiveByExtension(): void
    {
        $directoryManager = service(DirectoryManager::class);
        $files = iterator_to_array($directoryManager->recursiveFindByExtension(__DIR__, 'php'));
        $this->assertContains(__FILE__, $files);
    }

    public function testCreate(): void
    {
        $directoryManager = service(DirectoryManager::class);
        $path = __DIR__ . '/test-directory';

        if (file_exists($path)) {
            rmdir($path);
        }

        $directoryManager->create($path);
        self::assertDirectoryExists($path);

        if (file_exists($path)) {
            rmdir($path);
        }
    }
}
