<?php

declare(strict_types=1);

namespace Vigihdev\WpCliTools\Exceptions;

/**
 * Exception class for directory-related errors.
 * 
 */
final class DirectoryException extends WpCliToolsException
{
    public const NOT_FOUND = 5001;
    public const NOT_READABLE = 5002;
    public const NOT_WRITABLE = 5003;
    public const CANNOT_CREATE = 5004;
    public const CANNOT_DELETE = 5005;
    public const CANNOT_SCAN = 5006;
    public const NOT_EMPTY = 5007;


    /**
     * Direktori tidak ditemukan. Periksa apakah direktori ada di lokasi yang benar.
     * 
     * @param string $dirpath Path ke direktori
     * @return self
     */
    public static function notFound(string $dirpath): self
    {
        return new self(
            message: sprintf("Direktori %s tidak ditemukan: %s", basename($dirpath), $dirpath),
            code: self::NOT_FOUND,
            context: [
                'path' => $dirpath,
                'basename' => basename($dirpath),
                'dirname' => dirname($dirpath),
            ],
            solutions: [
                "Periksa apakah direktori ada di lokasi yang benar",
                "Periksa apakah direktori memiliki izin read (555) atau lebih",
                "Periksa apakah direktori dihapus atau diarahkan ke tempat lain",
                "Coba ulangi operasi setelah beberapa saat"
            ]
        );
    }


    /**
     * Direktori tidak dapat dibaca. Periksa izin (permission) atau atribut read-only.
     * 
     * @param string $dirpath Path ke direktori
     * @return self
     */
    public static function notReadable(string $dirpath): self
    {
        return new self(
            message: sprintf("Direktori %s tidak dapat dibaca: %s", basename($dirpath), $dirpath),
            code: self::NOT_READABLE,
            context: [
                'path' => $dirpath,
                'basename' => basename($dirpath),
                'dirname' => dirname($dirpath),
            ],
            solutions: [
                "Periksa apakah direktori memiliki izin read (555) atau lebih",
                "Periksa apakah direktori dihapus atau diarahkan ke tempat lain",
                "Coba ulangi operasi setelah beberapa saat"
            ],
        );
    }

    /**
     * Direktori tidak dapat ditulis. Periksa izin (permission) atau atribut read-only.
     * 
     * @param string $dirpath Path ke direktori
     * @return self
     */
    public static function notWritable(string $dirpath): self
    {
        return new self(
            message: sprintf("Direktori %s tidak dapat ditulis: %s. Periksa izin (permission) atau atribut read-only.", basename($dirpath), $dirpath),
            code: self::NOT_WRITABLE,
            context: [
                'path' => $dirpath,
                'basename' => basename($dirpath),
                'dirname' => dirname($dirpath),
            ],
            solutions: [
                "Periksa apakah direktori memiliki izin write (775) atau lebih",
                "Periksa apakah direktori dihapus atau diarahkan ke tempat lain",
                "Coba ulangi operasi setelah beberapa saat"
            ],
        );
    }

    /**
     * Tidak dapat membuat direktori. Periksa apakah direktori induk ada dan dapat ditulis.
     * 
     * @param string $dirpath Path ke direktori
     * @return self
     */
    public static function cannotCreate(string $dirpath): self
    {
        return new self(
            message: sprintf("Tidak dapat membuat direktori %s: %s", basename($dirpath), $dirpath),
            code: self::CANNOT_CREATE,
            context: [
                'path' => $dirpath,
                'basename' => basename($dirpath),
                'dirname' => dirname($dirpath),
            ],
            solutions: [
                "Periksa apakah direktori induk ada dan dapat ditulis",
                "Periksa apakah Anda memiliki izin untuk membuat direktori",
                "Periksa apakah ada batasan sistem operasi atau hosting",
                "Coba ulangi operasi setelah beberapa saat"
            ],
        );
    }

    /**
     * Tidak dapat menghapus direktori. Periksa apakah direktori kosong atau gunakan opsi penghapusan rekursif.
     * 
     * @param string $dirpath Path ke direktori
     * @return self
     */
    public static function cannotDelete(string $dirpath): self
    {
        return new self(
            message: sprintf("Tidak dapat menghapus direktori %s: %s", basename($dirpath), $dirpath),
            code: self::CANNOT_DELETE,
            context: [
                'path' => $dirpath,
                'basename' => basename($dirpath),
                'dirname' => dirname($dirpath),
            ],
            solutions: [
                "Periksa apakah direktori kosong atau gunakan opsi penghapusan rekursif",
                "Periksa apakah Anda memiliki izin untuk menghapus direktori",
                "Periksa apakah ada proses lain yang sedang menggunakan direktori",
                "Coba ulangi operasi setelah beberapa saat"
            ],
        );
    }

    /**
     * Tidak dapat memindai direktori. Periksa apakah direktori ada dan dapat dibaca.
     * 
     * @param string $dirpath Path ke direktori
     * @return self
     */
    public static function cannotScan(string $dirpath): self
    {
        return new self(
            message: sprintf("Tidak dapat memindai direktori %s: %s", basename($dirpath), $dirpath),
            code: self::CANNOT_SCAN,
            context: [
                'path' => $dirpath,
                'basename' => basename($dirpath),
                'dirname' => dirname($dirpath),
            ],
            solutions: [
                "Periksa apakah direktori ada dan dapat dibaca",
                "Periksa apakah Anda memiliki izin untuk memindai direktori",
                "Periksa apakah ada masalah dengan sistem file",
                "Coba ulangi operasi setelah beberapa saat"
            ],
        );
    }

    /**
     * Direktori tidak kosong. Periksa apakah direktori kosong atau gunakan opsi penghapusan rekursif.
     * 
     * @param string $dirpath Path ke direktori
     * @return self
     */
    public static function notEmpty(string $dirpath): self
    {
        return new self(
            message: sprintf("Direktori %s tidak kosong: %s", basename($dirpath), $dirpath),
            code: self::NOT_EMPTY,
            context: [
                'path' => $dirpath,
                'basename' => basename($dirpath),
                'dirname' => dirname($dirpath),
            ],
            solutions: [
                "Kosongkan direktori terlebih dahulu",
                "Gunakan opsi penghapusan rekursif",
                "Periksa apakah ada file atau subdirektori yang tidak terlihat",
                "Coba ulangi operasi setelah beberapa saat"
            ],
        );
    }
}
