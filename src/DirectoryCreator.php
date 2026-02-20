<?php

declare(strict_types=1);

namespace Medas\FileSystem;

use Medas\Core\{Attributes\Service, Interfaces\DirectoryCreator as DirectoryCreatorInterface};

#[Service]
readonly class DirectoryCreator implements DirectoryCreatorInterface
{
    public function create(string $path): void
    {
        // Normalize path separators
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

        // Check if already exists
        if (is_dir($path)) {
            return;
        }

        // Try to create with the recursive flag
        $success = @mkdir($path, 0755, true);

        if (!$success) {
            // Check if it was created by another process (race condition)
            if (is_dir($path)) {
                return;
            }

            throw new Exceptions\CantCreateDirectoryException($path, getcwd());
        }
    }
}
