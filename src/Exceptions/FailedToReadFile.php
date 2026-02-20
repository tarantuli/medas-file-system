<?php

declare(strict_types=1);

namespace Medas\FileSystem\Exceptions;

use Medas\Core\Exceptions\BaseException;

class FailedToReadFile extends BaseException
{
    public function __construct(string $path)
    {
        parent::__construct($path, error_get_last()['message'] ?? 'Unknown error');
    }

    public function pattern(): string
    {
        return 'Failed to read file %s: %s';
    }
}
