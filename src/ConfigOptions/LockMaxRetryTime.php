<?php

declare(strict_types=1);

namespace Medas\FileSystem\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption};

#[Service]
readonly class LockMaxRetryTime implements ConfigOption
{
    public function __construct(
        private FileSystemGroup $group,
    )
    {
    }

    public function group(): ConfigGroup
    {
        return $this->group;
    }

    public function name(): string
    {
        return 'lock-max-retry-time';
    }

    public function description(): string
    {
        return 'Maximum number of seconds to spend retrying a file lock before throwing an exception';
    }

    public function hasDefault(): bool
    {
        return true;
    }

    public function default(): float
    {
        return 1.0;
    }
}
