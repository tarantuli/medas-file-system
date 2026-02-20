# Medas File System

Secure file system utilities with path validation, file locking, and safe file operations.

## Features

- 🔒 Path traversal prevention
- 🔐 Directory whitelist security
- 🔄 File locking for concurrent access
- 📁 Safe directory creation
- 🗑️ Automatic temporary file cleanup

## Installation
```bash
composer require morphp/medas-file-system
```

## Usage

### Safe File Operations
```php
$writer = service(LockingFileWriter::class);

// Only works if path is in whitelist
$writer->write('/var/www/app/storage/file.txt', 'content');

// Throws PathNotInAllowedDirectory
$writer->write('/etc/passwd', 'malicious');
```

### File Locking
```php
$writer = service(LockingFileWriter::class);

// Automatically handles locking
$writer->write('/var/www/app/data.json', json_encode($data));
```

### Temporary Files
```php
$tempFiles = service(TemporaryFiles::class);

$tempPath = $tempFiles->create('temporary content');
// File automatically cleaned up on shutdown
```
