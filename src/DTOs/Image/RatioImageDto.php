<?php

declare(strict_types=1);

namespace Vigihdev\WpCliTools\DTOs\Image;

use Vigihdev\WpCliTools\Contracts\Image\RatioImageInterface;

final class RatioImageDto implements RatioImageInterface
{
    public function __construct(
        private readonly int $width,
        private readonly int $height,
    ) {}

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getRatio(): float
    {
        return $this->height > 0 ? $this->width / $this->height : 0;
    }
    public function getRatioString(): string
    {
        return $this->width . ':' . $this->height;
    }

    public function isLandscape(): bool
    {
        return $this->width > $this->height;
    }

    public function isPortrait(): bool
    {
        return $this->height > $this->width;
    }

    public function isSquare(): bool
    {
        return $this->width === $this->height;
    }

    public function getSimplifiedRatio(): string
    {
        $gcd = $this->calculateGCD($this->width, $this->height);
        return ($this->width / $gcd) . ':' . ($this->height / $gcd);
    }

    private function calculateGCD(int $a, int $b): int
    {
        return $b === 0 ? $a : $this->calculateGCD($b, $a % $b);
    }
}
