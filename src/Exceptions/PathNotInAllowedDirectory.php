<?php

declare(strict_types=1);

namespace Medas\FileSystem\Exceptions;

use Medas\Core\Exceptions\BaseException;

class PathNotInAllowedDirectory extends BaseException
{
    public function __construct(string $path, array $allowedPaths)
    {
        parent::__construct($path, implode(', ', $allowedPaths));
    }

    public function pattern(): string
    {
        return 'Path %s is not in allowed directories: %s';
    }
}
