<?php

declare(strict_types=1);

namespace Medas\FileSystem\Locking;

use Medas\FileSystem\Exceptions\{FailedToAcquireLockOnFile, FailedToOpenFile};

/**
 * Based on https://github.com/texthtml/php-lock, fetched on 2025-08-22
 */
class FileLock
{
    public const true EXCLUSIVE = true;
    public const false SHARED = false;
    public const true BLOCKING = true;
    public const false NON_BLOCKING = false;

    private mixed $handle = null;
    private readonly string $lockFile;

    public function __construct(
        string                    $path,
        private readonly Settings $settings,
    )
    {
        $this->lockFile = $this->settings->lockDirectory
            . DIRECTORY_SEPARATOR
            . sha1($path)
            . '.lock';
    }

    public function __destruct()
    {
        $this->release();
    }

    public function acquire(): void
    {
        if ($this->settings->exclusive === FileLock::EXCLUSIVE) {
            $lockType = "exclusive";
            $operation = LOCK_EX;
        }
        else {
            $lockType = "shared";
            $operation = LOCK_SH;
        }

        if ($this->settings->blocking === FileLock::NON_BLOCKING) {
            $operation |= LOCK_NB;
        }

        $startTimestamp = microtime(true);
        $sleepTime = 10000;

        do {
            if ($success = $this->flock($operation)) {
                break;
            }

            usleep($sleepTime);

            $sleepTime = min($sleepTime * 2, 1000000);
        } while (microtime(true) - $startTimestamp <= $this->settings->maxRetryTime);

        if (!$success) {
            throw new FailedToAcquireLockOnFile($lockType, $this->lockFile);
        }
    }

    public function release(): void
    {
        if ($this->handle === null) {
            return;
        }

        if ($this->settings->removeOnRelease && $this->flock(LOCK_EX | LOCK_NB)) {
            if (is_file($this->lockFile)) {
                unlink($this->lockFile);
            }
        }

        $this->flock(LOCK_UN);

        fclose($this->handle);

        $this->handle = null;
    }

    private function flock($operation): bool
    {
        if ($this->handle === null) {
            $this->handle = fopen($this->lockFile, "c");
        }

        if (!is_resource($this->handle)) {
            throw new FailedToOpenFile($this->lockFile);
        }

        return flock($this->handle, $operation);
    }
}
