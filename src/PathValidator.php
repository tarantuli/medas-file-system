<?php

declare(strict_types=1);

namespace Medas\FileSystem;

use Medas\Core\Attributes\{ConfigValue, Service};

#[Service]
readonly class PathValidator
{
    public function __construct(
        private PathNormalizer $pathNormalizer,

        #[ConfigValue(ConfigOptions\AllowedBasePaths::class)]
        private array          $allowedBasePaths = [],
    )
    {
    }

    /**
     * Validates that a path is within allowed directories
     *
     * @throws Exceptions\PathTraversalAttempt
     * @throws Exceptions\PathNotInAllowedDirectory
     */
    public function validate(string $path): string
    {
        // Check for path traversal attempts
        if (str_contains($path, '..')) {
            throw new Exceptions\PathTraversalAttempt($path);
        }

        // Normalize path separators
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

        // Resolve to the real path if it exists
        $realPath = realpath($path);

        if ($realPath === false) {
            // File doesn't exist yet - normalize the path
            $realPath = $this->pathNormalizer->normalize($path);
        }

        // Check against allowed base paths
        if (empty($this->allowedBasePaths)) {
            // No restrictions configured - allow all but warn
            trigger_error(
                'PathValidator has no allowed base paths configured - all paths are allowed!',
                E_USER_WARNING
            );

            return $realPath;
        }

        foreach ($this->allowedBasePaths as $basePath) {
            $realBase = realpath($basePath);

            if ($realBase && str_starts_with($realPath, $realBase)) {
                return $realPath;
            }
        }

        throw new Exceptions\PathNotInAllowedDirectory($path, $this->allowedBasePaths);
    }
}
