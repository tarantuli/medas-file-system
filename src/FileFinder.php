<?php

declare(strict_types=1);

namespace Medas\FileSystem;

use Medas\Core\{Attributes\Service, Interfaces\FileFinder as FileFinderInterface};

#[Service]
readonly class FileFinder implements FileFinderInterface
{
    public function find(string $directory, string $matchPattern, string $ignorePattern = null): \Generator
    {
        $handle = opendir($directory);

        while (false !== $entry = readdir($handle)) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $entry = $directory . DIRECTORY_SEPARATOR . $entry;

            if (file_exists($entry . DIRECTORY_SEPARATOR . '..')) {
                yield from $this->find($entry, $matchPattern, $ignorePattern);
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

    public function findByExtension(string $directory, string $extension, string $ignorePattern = null): \Generator
    {
        return $this->find($directory, sprintf('/\.%s$/i', preg_quote($extension)), $ignorePattern);
    }
}
