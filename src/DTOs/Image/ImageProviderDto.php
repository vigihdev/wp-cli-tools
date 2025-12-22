<?php

declare(strict_types=1);

namespace Vigihdev\WpCliTools\DTOs\Image;

use Vigihdev\WpCliTools\Contracts\Image\ImageProviderInterface;
use Vigihdev\WpCliTools\DTOs\Image\RatioImageDto;

final class ImageProviderDto implements ImageProviderInterface
{
    public function __construct(
        private readonly RatioImageDto $ratio,
        private readonly DimensionsImageDto $dimensions
    ) {}

    /**
     * @return RatioImageDto
     */
    public function getRatio(): RatioImageDto
    {
        return $this->ratio;
    }

    /**
     * @return DimensionsImageDto
     */
    public function getDimensions(): DimensionsImageDto
    {
        return $this->dimensions;
    }
}
