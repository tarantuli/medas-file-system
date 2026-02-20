<?php

declare(strict_types=1);

namespace Medas\FileSystem\Exceptions;

use Medas\Core\Exceptions\BaseException;

class DirectoryNotFound extends BaseException
{
    public function __construct(string $directory)
    {
        parent::__construct($directory);
    }

    public function pattern(): string
    {
        return 'Directory %s does not exist';
    }
}
