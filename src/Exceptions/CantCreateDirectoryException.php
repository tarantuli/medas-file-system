<?php

declare(strict_types=1);

namespace Medas\FileSystem\Exceptions;

use Medas\Core\Exceptions\BaseException;

class CantCreateDirectoryException extends BaseException
{
    public function __construct(string $directory, string $workingDir)
    {
        parent::__construct(
            $directory,
            $workingDir,
            error_get_last()['message'] ?? 'Unknown error'
        );
    }

    public function pattern(): string
    {
        return 'Failed to create directory %s (working directory: %s): %s';
    }
}
