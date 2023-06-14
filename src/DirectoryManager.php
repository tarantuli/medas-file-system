<?php

declare(strict_types=1);

namespace Medas\FileSystem;

use Medas\Core\Attributes\Service;
use Medas\Core\Interfaces\DirectoryManager as DirectoryManagerInterface;

#[Service]
class DirectoryManager implements DirectoryManagerInterface
{
    public function loadPhpFiles(string $directory): void
    {
        foreach ($this->recursiveFindByExtension($directory, 'php') as $fileName) {
            require_once $fileName;
        }
    }

    public function recursiveFindByExtension(string $directory, string $extension, string $ignorePattern = null): \Generator
    {
        return $this->recursiveFind($directory, sprintf('/\.%s$/i', preg_quote($extension)), $ignorePattern);
    }

    public function recursiveFind(string $directory, string $matchPattern, string $ignorePattern = null): \Generator
    {
        $handle = opendir($directory);

        while (false !== $entry = readdir($handle)) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $entry = $directory . DIRECTORY_SEPARATOR . $entry;

            if (is_dir($entry)) {
                yield from $this->recursiveFind($entry, $matchPattern, $ignorePattern);
                continue;
            }

            if (preg_match($matchPattern, $entry)) {
                if ($ignorePattern && preg_match($ignorePattern, $entry)) {
                    continue;
                }

                yield $entry;
            }
        }
    }

    public function recursiveFindByIterator(string $directory, string $pattern): \RegexIterator
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
        $workingDirectory = getcwd();

        if (str_starts_with($path, $workingDirectory)) {
            $currentDirectory = str_ends_with($workingDirectory, DIRECTORY_SEPARATOR)
                ? $workingDirectory
                : $workingDirectory . DIRECTORY_SEPARATOR;
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

            try {
                if (mkdir($currentDirectory) === false) {
                    throw new Exceptions\CantCreateDirectoryException($currentDirectory);
                }
            }
            catch (\Exception) {
                throw new Exceptions\CantCreateDirectoryException($currentDirectory);
            }
        }
    }
}
