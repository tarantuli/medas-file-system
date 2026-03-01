<?php

declare(strict_types=1);

namespace Medas\FileSystem\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption};

#[Service]
readonly class AllowedBasePaths implements ConfigOption
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
        return 'allowed-base-paths';
    }

    public function description(): string
    {
        return 'Array of allowed base paths for file operations. Prevents path traversal attacks by restricting file access to these directories.';
    }

    public function hasDefault(): bool
    {
        return true;
    }

    public function default(): array
    {
        return [getcwd()];
    }
}
