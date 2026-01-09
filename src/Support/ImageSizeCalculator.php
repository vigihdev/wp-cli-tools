<?php

declare(strict_types=1);

namespace Vigihdev\WpCliTools\Support;

use Imagick;
use SplFileInfo;
use Vigihdev\WpCliTools\DTOs\Image\{DimensionsImageDto, RatioImageDto};
use Vigihdev\WpCliTools\Exceptions\{ImageException, WpCliToolsException};
use Vigihdev\WpCliTools\Validators\FileValidator;

final class ImageSizeCalculator
{

    /**
     * File gambar yang akan diubah ukurannya
     * @var SplFileInfo $fileInfo File gambar yang akan diubah ukurannya
     */
    private readonly SplFileInfo $fileInfo;

    /**
     * Dimensi gambar asli
     * @var DimensionsImageDto $original Dimensi gambar asli
     */
    private readonly DimensionsImageDto $original;

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

        $this->fileInfo = new SplFileInfo($filepath);

        list($width, $height) = getimagesize($this->filepath);
        $this->original = new DimensionsImageDto($width, $height, $this->calculateRatio());
    }

    /**
     * Mendapatkan dimensi gambar asli
     *
     * @return DimensionsImageDto Dimensi gambar asli
     */
    public function getDimensions(): DimensionsImageDto
    {
        return $this->original;
    }

    /**
     * Mengubah ukuran gambar menjadi lebar tertentu, tinggi otomatis
     *
     * @param int $width Lebar yang diinginkan
     * @return DimensionsImageDto Dimensi gambar setelah diubah ukurannya
     */
    public function toWidth(int $width): DimensionsImageDto
    {
        $ratio = $this->getDimensions()->getOriginalRatio();
        $height = (int) round($width / $ratio->getRatio());
        return new DimensionsImageDto($width, $height, $ratio);
    }

    /**
     * Mengubah ukuran gambar menjadi tinggi tertentu, lebar otomatis
     *
     * @param int $height Tinggi yang diinginkan
     * @return DimensionsImageDto Dimensi gambar setelah diubah ukurannya
     */
    public function toHeight(int $height): DimensionsImageDto
    {
        $ratio = $this->getDimensions()->getOriginalRatio();
        $width = (int) round($height * $ratio->getRatio());
        return new DimensionsImageDto($width, $height, $ratio);
    }

    /**
     * Mengubah ukuran gambar menjadi ukuran tertentu, mempertahankan rasio asli
     *
     * @param int $width Lebar yang diinginkan
     * @param int $height Tinggi yang diinginkan
     * @return DimensionsImageDto Dimensi gambar setelah diubah ukurannya
     */
    public function fitWithin(int $maxWidth, int $maxHeight): DimensionsImageDto
    {
        $ratio = $this->getDimensions()->getOriginalRatio();

        // Hitung skala untuk width dan height
        $widthScale = $maxWidth / $ratio->getWidth();
        $heightScale = $maxHeight / $ratio->getHeight();

        // Pakai skala yang lebih kecil agar tidak melebihi kedua dimensi
        $scale = min($widthScale, $heightScale);

        $width = (int) round($ratio->getWidth() * $scale);
        $height = (int) round($ratio->getHeight() * $scale);

        return new DimensionsImageDto($width, $height, $ratio);
    }

    /**
     * Mengubah ukuran gambar menjadi ukuran tertentu, mempertahankan rasio asli
     *
     * @param int $width Lebar yang diinginkan
     * @param int $height Tinggi yang diinginkan
     * @return DimensionsImageDto Dimensi gambar setelah diubah ukurannya
     */
    public function fillArea(int $width, int $height): DimensionsImageDto
    {
        $ratio = $this->getDimensions()->getOriginalRatio();
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

        return new DimensionsImageDto($newWidth, $newHeight, $ratio);
    }

    /**
     * Menghitung dan mendapatkan rasio gambar
     *
     * @return RatioImageDto Rasio gambar yang telah dihitung
     */
    private function calculateRatio(): RatioImageDto
    {
        return $this->getRatio();
    }

    /** 
     * Membuat rasio gambar dari metadata
     * 
     * @return RatioImageDto Rasio gambar
     * 
     */
    private function getRatio(): RatioImageDto
    {
        if (extension_loaded('imagick')) {
            return $this->ratioFromImagick();
        }

        if (extension_loaded('gd')) {
            return $this->ratioFromGD();
        }

        return $this->ratioFromMetadata();
    }

    /**
     * Membuat rasio gambar dari Imagick
     * 
     * @return RatioImageDto Rasio gambar
     * 
     */
    private function ratioFromImagick(): RatioImageDto
    {
        if (!extension_loaded('imagick')) {
            throw ImageException::libraryNotAvailable('Imagick');
        }

        $image = new Imagick($this->filepath);
        $width = $image->getImageWidth();
        $height = $image->getImageHeight();

        return $this->createRatioDto($width, $height);
    }

    /** 
     * Membuat rasio gambar dari GD
     * 
     * @return RatioImageDto Rasio gambar
     * 
     */
    private function ratioFromGD(): RatioImageDto
    {
        if (!extension_loaded('gd')) {
            throw ImageException::libraryNotAvailable('GD');
        }

        $imageInfo = getimagesize($this->filepath);
        if ($imageInfo === false) {
            ImageException::invalidDimensions(0, 0);
        }

        [$width, $height] = $imageInfo;

        return $this->createRatioDto($width, $height);
    }

    /**
     * Membuat rasio gambar dari metadata
     * 
     * @return RatioImageDto Rasio gambar
     */
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

        throw ImageException::invalidDimensions(0, 0);
    }

    /**
     * Membuat rasio gambar dari dimensi
     * 
     * @param int $width Lebar gambar
     * @param int $height Tinggi gambar
     * @return RatioImageDto Rasio gambar
     * 
     */
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
