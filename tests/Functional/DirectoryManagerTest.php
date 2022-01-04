<?php

declare(strict_types=1);

namespace Medas\Test\Functional;

use Medas\FileSystem\DirectoryManager;
use PHPUnit\Framework\TestCase;

class DirectoryManagerTest extends TestCase
{
    public function testRecursiveByExtension(): void
    {
        $directoryManager = service(DirectoryManager::class);
        $files = iterator_to_array($directoryManager->recursiveFindByExtension(__DIR__, 'php'));
        $this->assertContains(__FILE__, $files);
        $this->assertArrayHasKey(__FILE__, $files);

    }
}
