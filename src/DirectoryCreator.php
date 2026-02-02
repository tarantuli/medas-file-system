<?php

declare(strict_types=1);

namespace Medas\FileSystem;

use Medas\Core\{Attributes\Service, Interfaces\DirectoryCreator as DirectoryCreatorInterface};

#[Service]
readonly class DirectoryCreator implements DirectoryCreatorInterface
{
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

            if (file_exists($currentDirectory) && file_exists($currentDirectory . DIRECTORY_SEPARATOR . '..')) {
                continue;
            }

            try {
                if (mkdir($currentDirectory) === false) {
                    if (file_exists($currentDirectory)) {
                        continue;
                    }

                    throw new Exceptions\CantCreateDirectoryException($currentDirectory);
                }
            }
            catch (\Exception) {
                if (file_exists($currentDirectory)) {
                    continue;
                }

                throw new Exceptions\CantCreateDirectoryException($currentDirectory);
            }
        }
    }
}
