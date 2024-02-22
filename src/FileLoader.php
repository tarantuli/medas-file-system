<?php

declare(strict_types=1);

namespace Medas\FileSystem;

use Medas\Core\{Attributes\Service, Interfaces\FileLoader as FileLoaderInterface};

#[Service]
readonly class FileLoader implements FileLoaderInterface
{
    public function __construct(
        private FileFinder $fileFinder,
    )
    {
    }

    public function load(string $directory): void
    {
        foreach ($this->fileFinder->findByExtension($directory, 'php') as $fileName) {
            require_once $fileName;
        }
    }
}
