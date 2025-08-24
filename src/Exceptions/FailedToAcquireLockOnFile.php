<?php

declare(strict_types=1);

namespace Medas\FileSystem\Exceptions;

use Medas\Core\Exceptions\BaseException;

class FailedToAcquireLockOnFile extends BaseException
{
    public function __construct(string $type, string $path)
    {
        parent::__construct($type, $path);
    }

    public function pattern(): string
    {
        return 'failed to acquire %s lock on file %s';
    }
}
