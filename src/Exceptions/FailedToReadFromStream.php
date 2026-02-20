<?php

declare(strict_types=1);

namespace Medas\FileSystem\Exceptions;

use Medas\Core\Exceptions\BaseException;

class FailedToReadFromStream extends BaseException
{
    public function __construct(string $fileName)
    {
        parent::__construct($fileName);
    }

    public function pattern(): string
    {
        return 'Failed to read from stream for file %s';
    }
}
