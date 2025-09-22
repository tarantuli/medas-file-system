<?php

declare(strict_types=1);

namespace Medas\FileSystem\Locking;

class Settings
{
    public function __construct(
        public string $lockDirectory,
        public bool  $exclusive = FileLock::EXCLUSIVE,
        public bool  $blocking = FileLock::NON_BLOCKING,
        public bool  $removeOnRelease = false,

        /** In seconds */
        public float $maxRetryTime = 1.0,
    )
    {
    }
}
