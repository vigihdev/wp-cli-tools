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
            message: sprintf("File %s tidak ditemukan", basename($filepath)),
            code: self::NOT_FOUND,
            context: [
                'path' => $filepath,
                'basename' => basename($filepath),
                'dirname' => dirname($filepath),
            ],
            solutions: [
                "Periksa apakah file ada di lokasi yang benar",
                "Periksa apakah file memiliki izin read (444) atau lebih",
                "Periksa apakah file dihapus atau diarahkan ke tempat lain",
                "Coba ulangi operasi setelah beberapa saat"
            ]
        );
    }

    public static function notReadable(string $filepath): self
    {
        return new self(
            message: sprintf("File %s tidak dapat dibaca", basename($filepath)),
            code: self::NOT_READABLE,
            context: [
                'path' => $filepath,
                'basename' => basename($filepath),
                'dirname' => dirname($filepath),
            ],
            solutions: [
                "Periksa apakah file memiliki izin read (444) atau lebih",
                "Periksa apakah file dihapus atau diarahkan ke tempat lain",
                "Coba ulangi operasi setelah beberapa saat"
            ],
        );
    }

    public static function notWritable(string $filepath): self
    {
        return new self(
            message: sprintf("File %s tidak dapat ditulis. Periksa izin (permission) atau atribut read-only.", basename($filepath)),
            code: self::NOT_WRITABLE,
            context: [
                'path' => $filepath,
                'basename' => basename($filepath),
                'dirname' => dirname($filepath),
            ],
            solutions: [
                "Periksa apakah file memiliki izin write (664) atau lebih",
                "Periksa apakah file dihapus atau diarahkan ke tempat lain",
                "Coba ulangi operasi setelah beberapa saat"
            ],
        );
    }

    public static function invalidExtension(string $filepath, string $expected): self
    {
        return new self(
            message: sprintf("File %s harus berekstensi .%s", basename($filepath), $expected),
            code: self::INVALID_EXTENSION,
            context: [
                'path' => $filepath,
                'basename' => basename($filepath),
                'dirname' => dirname($filepath),
            ],
            solutions: [
                "Periksa apakah file memiliki ekstensi yang sesuai",
                "Ubah ekstensi file menjadi .{$expected}",
                "Coba ulangi operasi setelah beberapa saat"
            ],
        );
    }

    public static function invalidMimeType(string $filepath, string $expected): self
    {
        return new self(
            message: sprintf("File %s harus berupa gambar dengan tipe mime %s", basename($filepath), $expected),
            code: self::INVALID_MIME_TYPE,
            context: [
                'path' => $filepath,
                'basename' => basename($filepath),
                'dirname' => dirname($filepath),
            ],
            solutions: [
                "Periksa apakah file memiliki tipe mime yang sesuai",
                "Ubah tipe mime file menjadi {$expected}",
                "Coba ulangi operasi setelah beberapa saat"
            ],
        );
    }


    public static function invalidJson(string $filepath, ?string $error = null): self
    {
        $message = sprintf("Format JSON %s tidak valid", basename($filepath));
        if ($error) {
            $message .= ": " . $error;
        }

        return new self(
            message: $message,
            code: self::INVALID_JSON,
            context: [
                'path' => $filepath,
                'basename' => basename($filepath),
                'dirname' => dirname($filepath),
            ],
            solutions: [
                "Periksa apakah file memiliki format JSON yang valid",
                "Ubah format file menjadi JSON yang valid",
                "Coba ulangi operasi setelah beberapa saat"
            ],
        );
    }

    public static function invalidXml(string $filepath, ?string $error = null): self
    {
        $message = sprintf("Format XML %s tidak valid", basename($filepath));
        if ($error) {
            $message .= ": " . $error;
        }

        return new self(
            message: $message,
            code: self::INVALID_XML,
            context: [
                'path' => $filepath,
                'basename' => basename($filepath),
                'dirname' => dirname($filepath),
            ],
            solutions: [
                "Periksa apakah file memiliki format XML yang valid",
                "Ubah format file menjadi XML yang valid",
                "Coba ulangi operasi setelah beberapa saat"
            ],
        );
    }

    public static function invalidCsv(string $filepath, ?string $error = null): self
    {
        $message = sprintf("Format CSV %s tidak valid", basename($filepath));
        if ($error) {
            $message .= ": " . $error;
        }

        return new self(
            message: $message,
            code: self::INVALID_CSV,
            context: [
                'path' => $filepath,
                'basename' => basename($filepath),
                'dirname' => dirname($filepath),
            ],
            solutions: [
                "Periksa apakah file memiliki format CSV yang valid",
                "Ubah format file menjadi CSV yang valid",
                "Coba ulangi operasi setelah beberapa saat"
            ],
        );
    }

    public static function fileTooLarge(string $filepath, int $maxSize, int $actualSize): self
    {
        return new self(
            message: sprintf(
                'File %s terlalu besar: %s (maksimal %s)',
                basename($filepath),
                size_format($actualSize),
                size_format($maxSize)
            ),
            code: self::FILE_TOO_LARGE,
            context: [
                'path' => $filepath,
                'basename' => basename($filepath),
                'dirname' => dirname($filepath),
            ],
            solutions: [
                "Periksa apakah file memiliki ukuran yang sesuai",
                "Ubah ukuran file menjadi ukuran yang sesuai",
                "Coba ulangi operasi setelah beberapa saat"
            ],
        );
    }

    public static function emptyFile(string $filepath): self
    {
        return new self(
            message: sprintf('File %s kosong atau tidak memiliki konten', basename($filepath)),
            code: self::EMPTY_FILE,
            context: [
                'path' => $filepath,
                'basename' => basename($filepath),
                'dirname' => dirname($filepath),
            ],
            solutions: [
                "Periksa apakah file memiliki konten",
                "Ubah konten file menjadi konten yang sesuai",
                "Coba ulangi operasi setelah beberapa saat"
            ],
        );
    }
}
