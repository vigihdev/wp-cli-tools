<?php

declare(strict_types=1);

namespace Vigihdev\WpCliTools\Exceptions;

final class FileException extends WpCliToolsException
{
    public const NOT_FOUND = 4001;
    public const NOT_READABLE = 4002;
    public const NOT_WRITABLE = 4003;
    public const INVALID_EXTENSION = 4004;
    public const INVALID_JSON = 4005;
    public const INVALID_XML = 4006;
    public const INVALID_CSV = 4007;
    public const FILE_TOO_LARGE = 4008;
    public const EMPTY_FILE = 4009;
    public const INVALID_MIME_TYPE = 4010;

    public static function notFound(string $filepath): self
    {
        return new self(
            sprintf("File %s tidak ditemukan: %s", basename($filepath), $filepath),
            self::NOT_FOUND
        );
    }

    public static function notReadable(string $filepath): self
    {
        return new self(
            sprintf("File %s tidak dapat dibaca: %s", basename($filepath), $filepath),
            self::NOT_READABLE
        );
    }

    public static function notWritable(string $filepath): self
    {
        return new self(
            sprintf("File %s tidak dapat ditulis: %s. Periksa izin (permission) atau atribut read-only.", basename($filepath), $filepath),
            self::NOT_WRITABLE
        );
    }

    public static function invalidExtension(string $filepath, string $expected): self
    {
        return new self(
            sprintf("File %s harus berekstensi .%s", basename($filepath), $expected),
            self::INVALID_EXTENSION
        );
    }

    public static function invalidMimeType(string $filepath, string $expected): self
    {
        return new self(
            sprintf("File %s harus berupa gambar dengan tipe mime %s", basename($filepath), $expected),
            self::INVALID_MIME_TYPE
        );
    }


    public static function invalidJson(string $filepath, ?string $error = null): self
    {
        $message = sprintf("Format JSON %s tidak valid", basename($filepath));
        if ($error) {
            $message .= ": " . $error;
        }

        return new self(
            $message,
            self::INVALID_JSON
        );
    }

    public static function invalidXml(string $filepath, ?string $error = null): self
    {
        $message = sprintf("Format XML %s tidak valid", basename($filepath));
        if ($error) {
            $message .= ": " . $error;
        }

        return new self(
            $message,
            self::INVALID_XML
        );
    }

    public static function invalidCsv(string $filepath, ?string $error = null): self
    {
        $message = sprintf("Format CSV %s tidak valid", basename($filepath));
        if ($error) {
            $message .= ": " . $error;
        }

        return new self(
            $message,
            self::INVALID_CSV
        );
    }

    public static function fileTooLarge(string $filepath, int $maxSize, int $actualSize): self
    {
        return new self(
            sprintf(
                'File %s terlalu besar: %s (maksimal %s)',
                basename($filepath),
                size_format($actualSize),
                size_format($maxSize)
            ),
            self::FILE_TOO_LARGE
        );
    }

    public static function emptyFile(string $filepath): self
    {
        return new self(
            sprintf('File %s kosong atau tidak memiliki konten', basename($filepath)),
            self::EMPTY_FILE
        );
    }
}
