<?php

declare(strict_types=1);

namespace Medas\FileSystem;

use Medas\ServiceManager\Attributes\Service;

#[Service]
class DirectoryManager
{
    public function loadPhpFiles(string $directory): void
    {
        foreach ($this->recursiveFindByExtension($directory, 'php') as $fileName) {
            require_once $fileName;
        }
    }

    public function recursiveFindByExtension(string $directory, string $extension): \RegexIterator
    {
        return $this->recursiveFind($directory, sprintf('/\.%s$/i', preg_quote($extension)));
    }

    public function recursiveFind(string $directory, string $pattern): \RegexIterator
    {
        return new \RegexIterator(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(
                    $directory,
                    \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS
                )
            ),
            $pattern
        );
    }

}
