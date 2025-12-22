<?php

declare(strict_types=1);

namespace Vigihdev\WpCliTools\Builders;

use Imagick;
use SplFileInfo;
use Vigihdev\WpCliTools\DTOs\Image\{DimensionsImageDto, ImageProviderDto, RatioImageDto};
use Vigihdev\WpCliTools\Exceptions\{FileException, WpCliToolsException};
use Vigihdev\WpCliTools\Validators\FileValidator;

final class ImageSizeBuilder
{
    private ?SplFileInfo $fileInfo = null;
    private ?RatioImageDto $ratioDto = null;

    /**
     * @param string $filepath Path ke file gambar
     * @throws WpCliToolsException
     */
    public function __construct(
        private readonly string $filepath
    ) {

        FileValidator::validate($filepath)
            ->mustExist()
            ->mustBeReadable()
            ->mustBeMimeType();

        if (!$this->fileInfo) {
            $this->fileInfo = new SplFileInfo($filepath);
        }
    }

    public function fromWidth(int $width): ImageProviderDto
    {
        $ratio = $this->getRatioDto();
        $height = (int) round($width / $ratio->getRatio());

        return new ImageProviderDto(ratio: $ratio, dimensions: new DimensionsImageDto($width, $height, $ratio));
    }

    public function fromHeight(int $height): ImageProviderDto
    {
        $ratio = $this->getRatioDto();
        $width = (int) round($height * $ratio->getRatio());

        return new ImageProviderDto(ratio: $ratio, dimensions: new DimensionsImageDto($width, $height, $ratio));
    }

    public function fitWithin(int $maxWidth, int $maxHeight): ImageProviderDto
    {
        $ratio = $this->getRatioDto();

        // Hitung skala untuk width dan height
        $widthScale = $maxWidth / $ratio->getWidth();
        $heightScale = $maxHeight / $ratio->getHeight();

        // Pakai skala yang lebih kecil agar tidak melebihi kedua dimensi
        $scale = min($widthScale, $heightScale);

        $width = (int) round($ratio->getWidth() * $scale);
        $height = (int) round($ratio->getHeight() * $scale);

        return new ImageProviderDto(ratio: $ratio, dimensions: new DimensionsImageDto($width, $height, $ratio));
    }

    public function fillArea(int $width, int $height): ImageProviderDto
    {
        $ratio = $this->getRatioDto();
        $imageRatio = $ratio->getRatio();
        $targetRatio = $width / $height;

        if ($imageRatio >= $targetRatio) {
            // Image lebih lebar, scale berdasarkan height
            $newHeight = $height;
            $newWidth = (int) round($height * $imageRatio);
        } else {
            // Image lebih tinggi, scale berdasarkan width
            $newWidth = $width;
            $newHeight = (int) round($width / $imageRatio);
        }

        return new ImageProviderDto(ratio: $ratio, dimensions: new DimensionsImageDto($newWidth, $newHeight, $ratio));
    }

    private function getRatioDto(): RatioImageDto
    {
        if ($this->ratioDto === null) {
            $this->ratioDto = $this->calculateRatio();
        }

        return $this->ratioDto;
    }


    private function calculateRatio(): RatioImageDto
    {
        return $this->getRatio();
    }

    private function getRatio(): RatioImageDto
    {
        if (extension_loaded('imagick')) {
            try {
                return $this->ratioFromImagick();
            } catch (\Exception $e) {
            }
        }

        if (extension_loaded('gd')) {
            try {
                return $this->ratioFromGD();
            } catch (\Exception $e) {
            }
        }

        return $this->ratioFromMetadata();
    }

    private function ratioFromImagick(): RatioImageDto
    {
        if (!extension_loaded('imagick')) {
            throw new \RuntimeException('Imagick extension is not available');
        }

        $image = new Imagick($this->filepath);
        $width = $image->getImageWidth();
        $height = $image->getImageHeight();

        return $this->createRatioDto($width, $height);
    }

    private function ratioFromGD(): RatioImageDto
    {
        if (!extension_loaded('gd')) {
            throw new \RuntimeException('GD extension is not available');
        }

        $imageInfo = getimagesize($this->filepath);
        if ($imageInfo === false) {
            throw new \RuntimeException('Failed to get image dimensions using GD');
        }

        [$width, $height] = $imageInfo;

        return $this->createRatioDto($width, $height);
    }

    private function ratioFromMetadata(): RatioImageDto
    {

        $imageInfo = getimagesize($this->filepath);
        if ($imageInfo !== false) {
            [$width, $height] = $imageInfo;
            return $this->createRatioDto($width, $height);
        }

        if (
            function_exists('exif_read_data') &&
            in_array(strtolower($this->fileInfo->getExtension()), ['jpg', 'jpeg'])
        ) {
            $exif = exif_read_data($this->filepath);
            if (isset($exif['COMPUTED']['Width'], $exif['COMPUTED']['Height'])) {
                return $this->createRatioDto(
                    $exif['COMPUTED']['Width'],
                    $exif['COMPUTED']['Height']
                );
            }
        }

        throw new \RuntimeException('Could not determine image dimensions from metadata');
    }

    private function createRatioDto(int $width, int $height): RatioImageDto
    {
        // Hitung greatest common divisor
        $gcd = gmp_intval(gmp_gcd($width, $height));

        // Jika GCD 0 (jarang terjadi), gunakan dimensi asli
        if ($gcd === 0) {
            $gcd = 1;
        }

        $ratioWidth = (int)($width / $gcd);
        $ratioHeight = (int)($height / $gcd);

        return new RatioImageDto($ratioWidth, $ratioHeight);
    }
}
