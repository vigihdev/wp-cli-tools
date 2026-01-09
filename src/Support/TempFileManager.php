<?php

declare(strict_types=1);

namespace Vigihdev\WpCliTools\Support;

use Symfony\Component\Filesystem\{Filesystem, Path};
use Vigihdev\WpCliTools\Exceptions\{DirectoryException, FileException};

final class TempFileManager
{
    private string $tempDir;


    /**
     * Constructor.
     */
    public function __construct()
    {

        $tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . hash('xxh64', __FILE__);
        if (!is_dir($tempDir)) {
            if (!mkdir($tempDir, 0755, true)) {
                throw DirectoryException::notWritable($tempDir);
            }
        }

        $this->tempDir = $tempDir;
    }

    /**
     * Read content from temp file.
     * 
     * @param string $filename
     * @return string
     */
    public function get(string $filename): string
    {
        $tempFile = Path::join($this->tempDir, $filename);
        if (!is_file($tempFile)) {
            throw FileException::notFound($tempFile);
        }
        return (string)file_get_contents($tempFile);
    }


    /**
     * Copy file to temp directory.
     * 
     * @param string $filepath Path of file to copy.
     * @return bool 
     */
    public function copy(string $filepath): bool
    {
        if (!is_file($filepath)) {
            throw FileException::notFound($filepath);
        }

        if (!is_writable($filepath)) {
            throw FileException::notWritable($filepath);
        }

        try {
            $filename = basename($filepath);
            $tempFile = Path::join($this->tempDir, $filename);
            $fs = new Filesystem();
            $fs->copy($filepath, Path::join($this->tempDir, $filename), true);
            return true;
        } catch (\Throwable $e) {
            throw FileException::notWritable($tempFile);
        }
    }

    /**
     * Write content to temp file.
     * 
     * @param string $filename Name of temp file.
     * @param string $content Content to write.
     * @return string
     */
    public function put(string $filename, string $content): string
    {
        $tempFile = $this->tempDir . DIRECTORY_SEPARATOR . $filename;
        if (file_put_contents($tempFile, $content)) {
            return $tempFile;
        }
        throw FileException::notWritable($tempFile);
    }

    /**
     * Get path of temp file.
     * 
     * @param string $filename Name of temp file.
     * @return string
     */
    public function getPath(string $filename): string
    {
        $tempFile = $this->tempDir . DIRECTORY_SEPARATOR . $filename;
        return $tempFile;
    }

    /**
     * Delete temp file.
     * 
     * @param string $filename Name of temp file.
     * @return bool
     */
    public function delete(string $filename): bool
    {
        if (is_file($this->getPath($filename))) {
            return (bool) unlink($this->getPath($filename));
        }
        return false;
    }

    /**
     * Check if temp file exists.
     * 
     * @param string $filename Name of temp file.
     * @return bool
     */
    public function exists(string $filename): bool
    {
        return (bool)is_file($this->getPath($filename));
    }
}
