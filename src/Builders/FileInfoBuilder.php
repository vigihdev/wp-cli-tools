<?php

declare(strict_types=1);

namespace Vigihdev\WpCliTools\Builders;

/**
 * Builder untuk mendapatkan informasi file
 */
final class FileInfoBuilder
{
    public function __construct(
        private readonly string $filePath,
    ) {}

    /**
     * Mendapatkan ekstensi file dari path yang ditentukan
     *
     * @return string Ekstensi file
     */
    public function getExtension(): string
    {
        $extension = pathinfo($this->filePath, PATHINFO_EXTENSION);
        return $extension;
    }

    /**
     * Mendapatkan nama file tanpa ekstensi
     *
     * @return string Nama file
     */
    public function getName(): string
    {
        $name = pathinfo($this->filePath, PATHINFO_FILENAME);
        return $name;
    }

    /**
     * Mendapatkan ukuran file dalam byte atau format yang diinginkan
     *
     * @param bool $formatted Apakah mengembalikan ukuran dalam format yang diinginkan (default: true)
     * @return string|int|float Ukuran file dalam byte atau format yang diinginkan
     */
    public function getSize($formatted = true): string|int|float
    {
        if (!file_exists($this->filePath)) {
            return $formatted ? '0 B' : 0;
        }

        $size = filesize($this->filePath);

        if ($size === false) {
            return $formatted ? '0 B' : 0;
        }

        if (!$formatted) {
            return $size;
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return round($size, 2) . ' ' . $units[$unitIndex];
    }


    /**
     * Mendapatkan path lengkap dari file
     *
     * @return string Path file
     */
    public function getPath(): string
    {
        return $this->filePath;
    }

    /**
     * Mendapatkan direktori dari path file
     *
     * @return string Direktori file
     */
    public function getDirectory(): string
    {
        $directory = pathinfo($this->filePath, PATHINFO_DIRNAME);
        return $directory;
    }
}
