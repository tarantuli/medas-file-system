<?php

declare(strict_types=1);

namespace Medas\FileSystem;

use Medas\ServiceManager\BasePackage;

class FileSystemPackage extends BasePackage
{

    public function dependencies(): array
    {
        return [];
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
