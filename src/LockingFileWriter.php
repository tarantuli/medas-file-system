<?php

declare(strict_types=1);

namespace Medas\FileSystem;

use Medas\Core\Attributes\Service;

#[Service]
readonly class LockingFileWriter
{
    public function __construct(
        private string $lockDirectory,
    )
    {
    }
    public function read(string $path): string
    {
        $lock = new Locking\FileLock($path, new Locking\Settings($this->lockDirectory, Locking\FileLock::SHARED));

        $lock->acquire();

        $contents = file_get_contents($path);

        $lock->release();

        return $contents;
    }

    public function write(string $path, string $contents, bool $append = false): void
    {
        $lock = new Locking\FileLock($path, new Locking\Settings($this->lockDirectory, Locking\FileLock::EXCLUSIVE));

        $lock->acquire();

        file_put_contents($path, $contents, $append ? FILE_APPEND : 0);

        $lock->release();
    }
}
