<?php

declare(strict_types=1);

namespace Medas\FileSystem;

use Medas\ServiceManager\Attributes\Service;
use Medas\ServiceManager\Interfaces\DirectoryManager as DirectoryManagerInterface;

#[Service]
class DirectoryManager implements DirectoryManagerInterface
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

    public function create(string $path): void
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            $path = str_replace('/', '\\', $path);
        }

        // Start in the working directory if possible
        if (str_starts_with($path, getcwd())) {
            $currentDirectory = getcwd() . DIRECTORY_SEPARATOR;
            $path = substr($path, strlen($currentDirectory));
        }
        else {
            $currentDirectory = '';
        }

        // Check each remaining part in order
        $parts = explode(DIRECTORY_SEPARATOR, $path);

        foreach ($parts as $part) {
            $currentDirectory .= $part . DIRECTORY_SEPARATOR;

            if (file_exists($currentDirectory) && is_dir($currentDirectory)) {
                continue;
            }

            if (@ mkdir($currentDirectory) === false) {
                throw new Exceptions\CantCreateDirectoryException($currentDirectory);
            }
        }
    }
}
