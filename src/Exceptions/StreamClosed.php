<?php

declare(strict_types=1);

namespace Medas\FileSystem\Exceptions;

use Medas\Core\Exceptions\BaseException;

class StreamClosed extends BaseException
{
    public function __construct(string $fileName)
    {
        parent::__construct($fileName);
    }

    public function pattern(): string
    {
        return 'Stream for file %s is closed';
    }
}
