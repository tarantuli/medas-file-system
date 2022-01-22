<?php

declare(strict_types=1);

namespace Medas\FileSystem\Exceptions;

use Medas\Core\Exceptions\BaseException;

class CantCreateDirectoryException extends BaseException
{
    public function __construct(string $directory)
    {
        parent::__construct($directory);
    }

    public function pattern(): string
    {
        return 'Failed to create directory %s';
    }
}
