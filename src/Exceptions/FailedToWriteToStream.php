<?php

declare(strict_types=1);

namespace Medas\FileSystem\Exceptions;

use Medas\Core\Exceptions\BaseException;

class FailedToWriteToStream extends BaseException
{
    public function __construct(string $fileName)
    {
        parent::__construct($fileName);
    }

    public function pattern(): string
    {
        return 'Failed to write to stream for file %s';
    }
}
