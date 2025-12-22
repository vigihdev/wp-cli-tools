<?php

declare(strict_types=1);

namespace Vigihdev\WpCliTools\Validators;

use Vigihdev\WpCliTools\Exceptions\ImageException;

final class ImageValidator
{
    /**
     * Format gambar yang didukung.
     */
    private const SUPPORTED_FORMATS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];

    private function __construct(
        private readonly string $imagePath,
    ) {}

    /**
     * Memvalidasi file gambar.
     *
     * @param string $imagePath Path ke file gambar
     * @return self
     */
    public static function validate(string $imagePath): self
    {
        return new self($imagePath);
    }

    /**
     * Memastikan file gambar ada.
     *
     * @return self
     * @throws ImageException
     */
    public function mustExist(): self
    {
        if (!file_exists($this->imagePath)) {
            throw ImageException::processingFailed("File not found: {$this->imagePath}");
        }
        return $this;
    }

    /**
     * Memastikan format gambar didukung.
     *
     * @return self
     * @throws ImageException
     */
    public function mustBeSupportedFormat(): self
    {
        $this->mustExist();

        $extension = strtolower(pathinfo($this->imagePath, PATHINFO_EXTENSION));

        if (!in_array($extension, self::SUPPORTED_FORMATS)) {
            throw ImageException::unsupportedFormat($extension);
        }

        return $this;
    }

    /**
     * Memastikan dimensi gambar valid.
     *
     * @return self
     * @throws ImageException
     */
    public function mustHaveValidDimensions(): self
    {
        $this->mustExist();
        $this->mustBeSupportedFormat();

        $imageInfo = @getimagesize($this->imagePath);

        if (!$imageInfo) {
            throw ImageException::processingFailed("Unable to read image dimensions");
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];

        if ($width <= 0 || $height <= 0) {
            throw ImageException::invalidDimensions($width, $height);
        }

        return $this;
    }

    /**
     * Memastikan rasio aspek gambar valid.
     *
     * @return self
     * @throws ImageException
     */
    public function mustHaveValidRatio(): self
    {
        $this->mustHaveValidDimensions();

        $imageInfo = getimagesize($this->imagePath);
        $width = $imageInfo[0];
        $height = $imageInfo[1];

        if ($height == 0) {
            throw ImageException::invalidRatio(0);
        }

        $ratio = $width / $height;

        if ($ratio <= 0) {
            throw ImageException::invalidRatio($ratio);
        }

        return $this;
    }

    /**
     * Memastikan gambar dapat diproses dengan library tertentu.
     *
     * @param string $library Nama library (gd, imagick, gmagick)
     * @return self
     * @throws ImageException
     */
    public function mustBeProcessableWith(string $library = 'gd'): self
    {
        $this->mustExist();
        $this->mustBeSupportedFormat();

        if (!extension_loaded($library)) {
            throw ImageException::libraryNotAvailable($library);
        }

        return $this;
    }

    /**
     * Memastikan skala persentase berada dalam batas yang diizinkan.
     *
     * @param float $percentage Persentase skala
     * @param float $min Persentase minimum (default 0.1)
     * @param float $max Persentase maksimum (default 500)
     * @return self
     * @throws ImageException
     */
    public function mustHaveValidScale(float $percentage, float $min = 0.1, float $max = 500): self
    {
        if ($percentage < $min || $percentage > $max) {
            throw ImageException::scaleOutOfBounds($percentage, $min, $max);
        }

        return $this;
    }

    /**
     * Mendapatkan informasi gambar.
     *
     * @return array
     * @throws ImageException
     */
    public function getInfo(): array
    {
        $this->mustExist();
        $this->mustBeSupportedFormat();

        $imageInfo = @getimagesize($this->imagePath);

        if (!$imageInfo) {
            throw ImageException::processingFailed("Unable to read image information");
        }

        return [
            'width' => $imageInfo[0],
            'height' => $imageInfo[1],
            'type' => $imageInfo[2],
            'mime' => $imageInfo['mime'],
            'ratio' => $imageInfo[1] > 0 ? $imageInfo[0] / $imageInfo[1] : 0,
            'format' => strtolower(pathinfo($this->imagePath, PATHINFO_EXTENSION)),
            'size' => filesize($this->imagePath),
        ];
    }
}
