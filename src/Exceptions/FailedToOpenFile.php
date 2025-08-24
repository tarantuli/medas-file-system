<?php

declare(strict_types=1);

namespace Medas\FileSystem\Exceptions;

use Medas\Core\Exceptions\BaseException;

class FailedToOpenFile extends BaseException
{
    public function __construct(string $path)
    {
        parent::__construct($path);
    }

    public function pattern(): string
    {
        return 'failed to open file %s';
    }
}
