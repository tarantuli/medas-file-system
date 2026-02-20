<?php

declare(strict_types=1);

namespace Medas\FileSystem;

class StreamReader
{
    /** @var resource */
    private $fh;

    private readonly string $fileName;

    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
        $this->fh = @fopen($fileName, 'rb');

        if ($this->fh === false) {
            throw new Exceptions\FailedToOpenFile($fileName);
        }
    }

    public function __destruct()
    {
        if (is_resource($this->fh)) {
            fclose($this->fh);
        }
    }

    public function seek(int $pos): bool
    {
        if (!is_resource($this->fh)) {
            throw new Exceptions\StreamClosed($this->fileName);
        }

        if ($pos >= 0) {
            return 0 === fseek($this->fh, $pos);
        }
        else {
            return 0 === fseek($this->fh, $pos, SEEK_END);
        }
    }

    public function forward(int $bytes): bool
    {
        if (!is_resource($this->fh)) {
            throw new Exceptions\StreamClosed($this->fileName);
        }

        return 0 === fseek($this->fh, $bytes, SEEK_CUR);
    }

    public function current(): int
    {
        if (!is_resource($this->fh)) {
            throw new Exceptions\StreamClosed($this->fileName);
        }

        $position = ftell($this->fh);

        if ($position === false) {
            throw new Exceptions\FailedToGetStreamPosition($this->fileName);
        }

        return $position;
    }

    public function read(int $bytes): string
    {
        if (!is_resource($this->fh)) {
            throw new Exceptions\StreamClosed($this->fileName);
        }

        $contents = fread($this->fh, $bytes);

        if ($contents === false) {
            throw new Exceptions\FailedToReadFromStream($this->fileName);
        }

        return $contents;
    }

    public function write(string $contents): bool
    {
        if (!is_resource($this->fh)) {
            throw new Exceptions\StreamClosed($this->fileName);
        }

        $result = fwrite($this->fh, $contents);

        if ($result === false) {
            throw new Exceptions\FailedToWriteToStream($this->fileName);
        }

        return true;
    }
}
