<?php

declare(strict_types=1);

namespace Vigihdev\WpCliTools\Contracts\Image;

interface ImageProviderInterface
{
    public function getRatio(): RatioImageInterface;
    public function getDimensions(): DimensionsImageInterface;
}
