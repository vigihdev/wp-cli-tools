<?php

declare(strict_types=1);

namespace Vigihdev\WpCliTools\Contracts\Image;

interface DimensionsImageInterface
{
    public function getWidth(): int;
    public function getHeight(): int;
    public function getOriginalRatio(): RatioImageInterface;
}
