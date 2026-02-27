<?php

declare(strict_types=1);

namespace Medas\FileSystem;

use Medas\Core\Attributes\Service;

#[Service]
class TemporaryFiles
{
    private array $temporaryFiles = [];

    public function __construct()
    {
        register_shutdown_function(fn() => $this->delete());
    }

    private function delete(): void
    {
        foreach ($this->temporaryFiles as $file) {
            if (file_exists($file)) {
                @unlink($file);
            }
        }
    }

    public function create($content = null): string
    {
        $tempFilename = tempnam(sys_get_temp_dir(), 'file');

        if ($tempFilename === false) {
            throw new Exceptions\CantCreateTemporaryFileException(sys_get_temp_dir());
        }

        $this->temporaryFiles[] = $tempFilename;

        if ($content !== null) {
            $fh = @fopen($tempFilename, 'w');

            if ($fh === false) {
                throw new Exceptions\FailedToOpenFile($tempFilename);
            }

            $result = fwrite($fh, $content);

            if ($result === false) {
                fclose($fh);

                throw new Exceptions\FailedToWriteFile($tempFilename);
            }

            fclose($fh);
        }

        return $tempFilename;
    }
}
