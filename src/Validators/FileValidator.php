<?php

declare(strict_types=1);

namespace Vigihdev\WpCliTools\Validators;

use Vigihdev\WpCliTools\Exceptions\FileException;

final class FileValidator
{
    public function __construct(
        private readonly string $filepath
    ) {}

    public static function validate(string $filepath): static
    {
        return new self($filepath);
    }

    /**
     * Validasi bahwa file ada
     * 
     * @throws FileException
     */
    public function mustExist(): self
    {
        if (!file_exists($this->filepath)) {
            throw FileException::notFound($this->filepath);
        }

        return $this;
    }

    /**
     * Validasi bahwa path adalah file (bukan direktori)
     * 
     * @throws FileException
     */
    public function mustBeFile(): self
    {
        $this->mustExist();

        if (!is_file($this->filepath)) {
            throw FileException::invalidExtension(
                $this->filepath,
                'file',
            );
        }

        return $this;
    }

    /**
     * Validasi bahwa file dapat dibaca
     * 
     * @throws FileException
     */
    public function mustBeReadable(): self
    {
        $this->mustExist();

        if (!is_readable($this->filepath)) {
            throw FileException::notReadable($this->filepath);
        }

        return $this;
    }

    public function mustBeMimeType(): self
    {
        $mimeType = mime_content_type($this->filepath);
        if (!str_starts_with($mimeType, 'image/')) {
            throw FileException::invalidMimeType($this->filepath, 'image');
        }

        return $this;
    }

    /**
     * Validasi bahwa file dapat ditulis
     * 
     * @throws FileException
     */
    public function mustBeWritable(): self
    {
        $this->mustExist();

        if (!is_writable($this->filepath)) {
            throw FileException::notWritable($this->filepath);
        }

        return $this;
    }

    /**
     * Validasi extension file
     * 
     * @throws FileException
     */
    public function mustBeExtension(string $extension): self
    {
        $ext = strtolower(pathinfo($this->filepath, PATHINFO_EXTENSION));
        if ($ext !== strtolower($extension)) {
            throw FileException::invalidExtension($this->filepath, $extension);
        }

        return $this;
    }

    /**
     * Validasi bahwa file adalah JSON
     * 
     * @throws FileException
     */
    public function mustBeJson(): self
    {
        return $this->mustBeExtension('json');
    }

    /**
     * Validasi bahwa file adalah XML
     * 
     * @throws FileException
     */
    public function mustBeXml(): self
    {
        return $this->mustBeExtension('xml');
    }

    /**
     * Validasi bahwa file adalah CSV
     * 
     * @throws FileException
     */
    public function mustBeCsv(): self
    {
        return $this->mustBeExtension('csv');
    }

    /**
     * Validasi bahwa JSON valid
     * 
     * @throws FileException
     */
    public function mustBeValidJson(): self
    {
        $this->mustBeJson();
        $this->mustBeReadable();

        $content = file_get_contents($this->filepath);
        json_decode($content);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw FileException::invalidJson($this->filepath, json_last_error_msg());
        }

        return $this;
    }

    /**
     * Validasi bahwa XML valid
     * 
     * @throws FileException
     */
    public function mustBeValidXml(): self
    {
        $this->mustBeXml();
        $this->mustBeReadable();

        libxml_use_internal_errors(true);
        $xml = simplexml_load_file($this->filepath);

        if ($xml === false) {
            $errors = libxml_get_errors();
            $errorMsg = !empty($errors) ? $errors[0]->message : 'Unknown error';
            libxml_clear_errors();

            throw FileException::invalidXml($this->filepath, trim($errorMsg));
        }

        return $this;
    }

    /**
     * Validasi bahwa file tidak kosong
     * 
     * @throws FileException
     */
    public function mustNotBeEmpty(): self
    {
        $this->mustExist();

        if (filesize($this->filepath) === 0) {
            throw FileException::emptyFile($this->filepath);
        }

        return $this;
    }

    /**
     * Validasi ukuran file
     * 
     * @throws FileException
     */
    public function mustNotExceedSize(int $maxSize): self
    {
        $this->mustExist();

        $actualSize = filesize($this->filepath);
        if ($actualSize > $maxSize) {
            throw FileException::fileTooLarge($this->filepath, $maxSize, $actualSize);
        }

        return $this;
    }
}
