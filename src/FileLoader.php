<?php

declare(strict_types=1);

namespace Medas\FileSystem;

use Medas\Core\{Attributes\Service, Interfaces\FileLoader as FileLoaderInterface};

#[Service]
readonly class FileLoader implements FileLoaderInterface
{
    public function __construct(
        private FileFinder    $fileFinder,
        private PathValidator $pathValidator,
    )
    {
    }

    public function load(string $directory): void
    {
        // Validate directory is allowed
        $validatedDir = $this->pathValidator->validate($directory);

        foreach ($this->fileFinder->findByExtension($validatedDir, 'php') as $fileName) {
            // Validate each file is still in the allowed directory
            $validatedFile = $this->pathValidator->validate($fileName);

            // Verify the file is actually in the original directory (prevent symlink attacks)
            if (!str_starts_with($validatedFile, $validatedDir)) {
                throw new Exceptions\PathTraversalAttempt($fileName);
            }

            require_once $validatedFile;
        }
    }
}
