<?php

declare(strict_types=1);

namespace Medas\FileSystem\Exceptions;

use Medas\Core\Exceptions\BaseException;

class CantCreateTemporaryFileException extends BaseException
{
    public function pattern(): string
    {
        return 'cannot create temporary file';
    }
}
