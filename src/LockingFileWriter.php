<?php

declare(strict_types=1);

namespace Medas\FileSystem;

use Medas\Core\Attributes\{ConfigValue, Service};

#[Service]
readonly class LockingFileWriter
{
    public function __construct(
        private PathValidator $pathValidator,
        private string        $lockDirectory,

        #[ConfigValue(ConfigOptions\LockMaxRetryTime::class)]
        private float         $lockMaxRetryTime = 1.0,
    )
    {
        if (!is_dir($this->lockDirectory)) {
            new DirectoryCreator()->create($this->lockDirectory);
        }
    }

    public function read(string $path): string
    {
        // Validate the path first
        $validPath = $this->pathValidator->validate($path);

        $lock = new Locking\FileLock(
            $validPath,
            new Locking\Settings(
                $this->lockDirectory,
                Locking\FileLock::SHARED,
                maxRetryTime: $this->lockMaxRetryTime
            )
        );

        try {
            $lock->acquire();

            $contents = @file_get_contents($validPath);

            if ($contents === false) {
                throw new Exceptions\FailedToReadFile($validPath);
            }

            return $contents;
        }

        finally{
            $lock->release();
        }
    }

    public function write(string $path, string $contents, bool $append = false): void
    {
        // Validate the path first
        $validPath = $this->pathValidator->validate($path);

        $lock = new Locking\FileLock(
            $validPath,
            new Locking\Settings(
                $this->lockDirectory,
                Locking\FileLock::EXCLUSIVE,
                maxRetryTime: $this->lockMaxRetryTime
            )
        );

        try {
            $lock->acquire();

            $result = @file_put_contents($validPath, $contents, $append ? FILE_APPEND : 0);

            if ($result === false) {
                throw new Exceptions\FailedToWriteFile($validPath);
            }
        }

        finally{
            $lock->release();
        }
    }
}
