<?php

declare(strict_types=1);

namespace Medas\FileSystem;

class StreamReader
{
    /** @var resource */
    private $fh;

    public function __construct(string $fileName)
    {
        $this->fh = fopen($fileName, 'rb');
    }

    public function __destruct()
    {
        if ($this->fh) {
            fclose($this->fh);
        }
    }

    public function seek(int $pos): bool
    {
        if ($pos >= 0) {
            return 0 === fseek($this->fh, $pos);
        }
        else {
            return 0 === fseek($this->fh, $pos, SEEK_END);
        }
    }

    public function forward(int $bytes): bool
    {
        return 0 === fseek($this->fh, $bytes, SEEK_CUR);
    }

    public function current(): int
    {
        return ftell($this->fh);
    }

    public function read(int $bytes): string
    {
        return fread($this->fh, $bytes);
    }

    public function write(string $contents): bool
    {
        return false !== fwrite($this->fh, $contents);
    }
}
