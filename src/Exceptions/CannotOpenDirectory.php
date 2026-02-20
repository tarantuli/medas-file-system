<?php

declare(strict_types=1);

namespace Medas\FileSystem\Exceptions;

use Medas\Core\Exceptions\BaseException;

class CannotOpenDirectory extends BaseException
{
    public function __construct(string $directory)
    {
        parent::__construct($directory);
    }

    public function pattern(): string
    {
        return 'Cannot open directory %s';
    }
}
