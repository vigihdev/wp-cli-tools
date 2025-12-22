<?php

declare(strict_types=1);

namespace Vigihdev\WpCliTools\Exceptions;

final class FilesException extends WpCliToolsException
{
    public const FILE_NOT_FOUND     = 1001;
    public const FILE_NOT_READABLE  = 1002;
    public const FILE_NOT_WRITABLE  = 1003;
    public const FILE_INVALID_TYPE  = 1004;
    public const FILE_TOO_LARGE     = 1005;

    public static function notFound(string $path): self
    {
        return new self(
            message: sprintf("File not found: %s", $path),
            code: self::FILE_NOT_FOUND,
            context: ['path' => $path],
            solutions: [
                "Check if the file path is correct and absolute/relative properly",
                "Verify the file actually exists on the server",
                "Look for typos in the filename or directory name",
                "Use WP_CLI::debug() or var_dump() to confirm the path value"
            ]
        );
    }

    public static function notReadable(string $path): self
    {
        $permissions = file_exists($path) ? substr(sprintf('%o', fileperms($path)), -4) : 'N/A';

        return new self(
            message: sprintf("File is not readable: %s", $path),
            code: self::FILE_NOT_READABLE,
            context: [
                'path' => $path,
                'permissions' => $permissions
            ],
            solutions: [
                "Check file permissions — should be at least 644 for files",
                "Ensure the web server/PHP process user (www-data, apache, etc.) has read access",
                "Run `chmod 644 {$path}` or `chmod +r {$path}` if needed",
                "Check if the file is locked by another process",
                "Verify the directory containing the file is traversable (execute permission)"
            ]
        );
    }

    public static function notWritable(string $path): self
    {
        $permissions = file_exists($path) ? substr(sprintf('%o', fileperms($path)), -4) : 'N/A';

        return new self(
            message: sprintf("File is not writable: %s", $path),
            code: self::FILE_NOT_WRITABLE,
            context: [
                'path' => $path,
                'permissions' => $permissions
            ],
            solutions: [
                "Check file permissions — should be at least 664 or 666 for writability",
                "Ensure the web server/PHP process has write access",
                "Run `chmod 664 {$path}` or `chmod +w {$path}`",
                "Check directory permissions — parent directory needs write permission too",
                "Verify no immutable flag or SELinux restrictions"
            ]
        );
    }

    public static function invalidImage(string $path, ?string $expectedType = null): self
    {
        $message = $expectedType
            ? sprintf("File is not a valid %s image: %s", strtoupper($expectedType), $path)
            : sprintf("File is not a valid image: %s", $path);

        $mime = file_exists($path) ? mime_content_type($path) ?: 'unknown' : 'unknown';

        return new self(
            message: $message,
            code: self::FILE_INVALID_TYPE,
            context: [
                'path' => $path,
                'mime_type' => $mime,
                'expected_type' => $expectedType
            ],
            solutions: [
                "Verify the file is a real image and not corrupted",
                "Check the file extension matches the actual format",
                "Try opening the file in an image viewer or editor",
                "Re-save/export the image from a reliable source",
                "Ensure the file wasn't truncated during upload/transfer"
            ]
        );
    }

    public static function tooLarge(string $path, int $actualSize, int $maxSize): self
    {
        return new self(
            message: sprintf(
                "File too large: %s (%s bytes exceeds limit of %s bytes)",
                $path,
                number_format($actualSize),
                number_format($maxSize)
            ),
            code: self::FILE_TOO_LARGE,
            context: [
                'path' => $path,
                'size_bytes' => $actualSize,
                'max_bytes' => $maxSize,
                'size_human' => size_format($actualSize),
                'max_human' => size_format($maxSize)
            ],
            solutions: [
                "Compress or resize the file before processing",
                "Increase the allowed file size limit in your configuration",
                "Check PHP settings: upload_max_filesize and post_max_size in php.ini",
                "Split large operations into smaller chunks if possible"
            ]
        );
    }
}
