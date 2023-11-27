<?php

declare(strict_types=1);

namespace Medas\FileSystem;

use Medas\Core\Attributes\Service;

#[Service]
class PathNormalizer
{
    private const SLASH = '/';
    private const BACKSLASH = '\\';
    private const DOUBLE_BACKSLASH = '\\\\';

    /**
     * Turns the given path into an absolute path without . and .. directories
     */
    public function normalize(
        string $path,
        string $directorySeparator = DIRECTORY_SEPARATOR,
        string $workingDirectory = null,
    ): string
    {
        $workingDirectory ??= getcwd();

        if (str_starts_with($path, '.')) {
            $path = $workingDirectory . $directorySeparator . $path;
        }

        if ($this->isRelative($path)) {
            $path = $workingDirectory . $directorySeparator . $path;
        }

        $startsWithDoubleBackslash = str_starts_with($path, self::DOUBLE_BACKSLASH);
        $path = str_replace(self::BACKSLASH, self::SLASH, $path);
        $startsWithSlash = (!$startsWithDoubleBackslash) && str_starts_with($path, self::SLASH);
        $parts = array_filter(explode(self::SLASH, $path), fn($part) => strlen($part));
        $absolutes = [];

        foreach ($parts as $part) {
            if ('.' === $part) {
                continue;
            }

            if ('..' === $part) {
                array_pop($absolutes);
            }
            else {
                $absolutes[] = $part;
            }
        }

        if ($startsWithDoubleBackslash) {
            $path = self::DOUBLE_BACKSLASH . implode(self::BACKSLASH, $absolutes);
        }
        elseif ($startsWithSlash) {
            $path = self::SLASH . implode(self::SLASH, $absolutes);
        }
        else {
            $path = implode($directorySeparator, $absolutes);
        }

        return $path;
    }

    private function isRelative(string $path): bool
    {
        // An absolute path should either:
        // - Start with a slash (unix)
        // - Start with a double backslash (network paths on Windows)
        // - Have a colon on the second position (Windows)
        return !str_starts_with($path, self::SLASH)
            && !str_starts_with($path, self::DOUBLE_BACKSLASH)
            && substr($path, 1, 1) !== ':';
    }
}
