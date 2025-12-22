<?php

declare(strict_types=1);

namespace Vigihdev\WpCliTools\DTOs\Image;

use Vigihdev\WpCliTools\Contracts\Able\StringAbleInterface;
use Vigihdev\WpCliTools\Contracts\Image\DimensionsImageInterface;

final class DimensionsImageDto implements DimensionsImageInterface, StringAbleInterface
{
    public function __construct(
        private readonly int $width,
        private readonly int $height,
        private readonly RatioImageDto $originalRatio,
    ) {}

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getOriginalRatio(): RatioImageDto
    {
        return $this->originalRatio;
    }

    public function getAspectRatio(): float
    {
        return $this->height > 0 ? $this->width / $this->height : 0;
    }

    public function toString(): string
    {
        return sprintf(
            '%dx%d (Ratio: %s)',
            $this->width,
            $this->height,
            $this->originalRatio->getSimplifiedRatio()
        );
    }
}
