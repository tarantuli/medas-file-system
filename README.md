# medas-file-system

Part of the [Medas framework](https://github.com/tarantuli/medas-core).

## Description

Secure filesystem utilities with path traversal prevention, directory whitelisting, advisory file locking, and safe file I/O. All path-sensitive operations go through `PathValidator`, which rejects `..` segments and enforces a configurable list of allowed base directories.

**Services provided:**

| Class               | Purpose                                                                                                                     |
|---------------------|-----------------------------------------------------------------------------------------------------------------------------|
| `PathValidator`     | Validates a path against `..` traversal and a whitelist of allowed base directories                                         |
| `PathNormalizer`    | Resolves relative paths and `.`/`..` segments to absolute paths without touching the filesystem                             |
| `LockingFileWriter` | Reads and writes files under an exclusive/shared advisory lock; validates paths before every operation                      |
| `DirectoryCreator`  | Recursively creates directories; race-condition safe (ignores `mkdir` failures if the directory was created concurrently)   |
| `FileFinder`        | Recursively yields file paths matching a regex or extension; yields from a generator to avoid loading all paths into memory |
| `FileLoader`        | `require_once`s all PHP files in a validated directory, with a secondary check against symlink-based traversal              |
| `TemporaryFiles`    | Creates temp files and deletes them automatically on PHP shutdown                                                           |
| `StreamReader`      | Byte-level random-access reader with `seek`, `forward`, `current`, `read`, and `write`                                      |

`LockingFileWriter` uses a dedicated lock directory where a SHA1-named `.lock` file is created per target path. It retries with exponential back-off up to the configured `lock-max-retry-time`.

## Configuration options

| Option                            | Default      | Description                                                                   |
|-----------------------------------|--------------|-------------------------------------------------------------------------------|
| `file-system.allowed-base-paths`  | `[getcwd()]` | Directories within which file operations are permitted                        |
| `file-system.lock-max-retry-time` | `1.0`        | Seconds to retry acquiring a lock before throwing `FailedToAcquireLockOnFile` |

## Usage

### Package developer context

Register the package and inject the services you need:

```php
use Medas\FileSystem\FileSystemPackage;

FileSystemPackage::instance();
```

**Validating a path:**

```php
use Medas\FileSystem\PathValidator;
use Medas\Core\Attributes\Service;

#[Service]
readonly class SafeStorage
{
    public function __construct(
        private PathValidator $pathValidator,
    ) {}

    public function resolveUploadPath(string $filename): string
    {
        // Throws PathTraversalAttempt if $filename contains '..'
        // Throws PathNotInAllowedDirectory if the resolved path is outside the whitelist
        return $this->pathValidator->validate('var/uploads/' . $filename);
    }
}
```

**Reading and writing files with locking:**

```php
use Medas\FileSystem\LockingFileWriter;
use Medas\Core\Attributes\Service;

#[Service]
readonly class JsonStore
{
    public function __construct(
        private LockingFileWriter $writer,
    ) {}

    public function save(string $path, array $data): void
    {
        // Acquires an exclusive lock, validates path, writes, releases lock
        $this->writer->write($path, json_encode($data, JSON_PRETTY_PRINT));
    }

    public function append(string $path, string $line): void
    {
        $this->writer->write($path, $line . "\n", append: true);
    }

    public function load(string $path): array
    {
        // Acquires a shared lock, validates path, reads, releases lock
        $json = $this->writer->read($path);

        return json_decode($json, true);
    }
}
```

**Creating directories safely:**

```php
use Medas\FileSystem\DirectoryCreator;

// Creates all intermediate directories; no-ops if the path already exists
$directoryCreator->create('var/cache/responses/2026/05');
```

**Finding files recursively:**

```php
use Medas\FileSystem\FileFinder;
use Medas\Core\Attributes\Service;

#[Service]
readonly class TemplateLoader
{
    public function __construct(
        private FileFinder $fileFinder,
    ) {}

    public function findTemplates(string $directory): array
    {
        $paths = [];

        // Find all .html files, ignoring anything in a 'draft' subdirectory
        foreach ($this->fileFinder->findByExtension($directory, 'html', '/\/draft\//') as $path) {
            $paths[] = $path;
        }

        return $paths;
    }

    public function findByPattern(string $directory): void
    {
        // Full regex match against the absolute path
        foreach ($this->fileFinder->find($directory, '/Controller\.php$/') as $path) {
            // process $path
        }
    }
}
```

**Temporary files with automatic cleanup:**

```php
use Medas\FileSystem\TemporaryFiles;
use Medas\Core\Attributes\Service;

#[Service]
readonly class ImageProcessor
{
    public function __construct(
        private TemporaryFiles $tempFiles,
    ) {}

    public function process(string $imageBinary): string
    {
        // File is written immediately and deleted on PHP shutdown
        $tempPath = $this->tempFiles->create($imageBinary);

        // Pass the temp path to an external tool
        exec("convert $tempPath -resize 800x600 $tempPath");

        return file_get_contents($tempPath);
    }
}
```

**Random-access stream reading:**

```php
use Medas\FileSystem\StreamReader;

$reader = new StreamReader('/path/to/binary.dat');

// Jump to byte offset 128
$reader->seek(128);

// Read 4 bytes
$header = $reader->read(4);

// Skip forward 16 bytes
$reader->forward(16);

// Get the current position
$pos = $reader->current();

// Seek from the end (negative offset)
$reader->seek(-8);
$trailer = $reader->read(8);
```

**Normalising a path without filesystem access:**

```php
use Medas\FileSystem\PathNormalizer;

$normalizer = service(PathNormalizer::class);

// Resolves . and .. without calling realpath()
$absolute = $normalizer->normalize('var/../cache/./responses');
// → /your/project/cache/responses
```

### Backend user context

**Configuring the path whitelist:**

```yaml
file-system:
  allowed-base-paths:
    - /var/www/html/storage
    - /var/www/html/public/uploads
  lock-max-retry-time: 2.0
```

If `allowed-base-paths` is not configured it defaults to `getcwd()` and emits an `E_USER_WARNING`. Any write attempt to a path outside the whitelist throws `PathNotInAllowedDirectory`; any path containing `..` throws `PathTraversalAttempt`.

**Lock directory** — `LockingFileWriter` takes a lock directory as a constructor argument (injected by the DI container). Ensure the directory is writable by the PHP process and is not web-accessible.

**Concurrency** — `LockingFileWriter` uses PHP advisory locking (`flock`). Multiple processes writing to the same file will queue up and retry with exponential back-off. Increase `lock-max-retry-time` if your workload involves high contention on a single file.
