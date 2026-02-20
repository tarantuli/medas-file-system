<?php

declare(strict_types=1);

namespace Medas\FileSystem\Exceptions;

use Medas\Core\Exceptions\BaseException;

class PathTraversalAttempt extends BaseException
{
    public function __construct(string $path)
    {
        parent::__construct($path);
    }

    public function pattern(): string
    {
        return 'Path traversal attempt detected: %s';
    }
}
