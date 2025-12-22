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

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @return float
     */
    public function getRatio(): float
    {
        return $this->height > 0 ? $this->width / $this->height : 0;
    }

    /**
     * @return string
     */
    public function getRatioString(): string
    {
        return $this->width . ':' . $this->height;
    }

    /**
     * @return bool
     */
    public function isLandscape(): bool
    {
        return $this->width > $this->height;
    }

    /**
     * @return bool
     */
    public function isPortrait(): bool
    {
        return $this->height > $this->width;
    }

    /**
     * @return bool
     */
    public function isSquare(): bool
    {
        return $this->width === $this->height;
    }

    /**
     * @return string
     */
    public function getSimplifiedRatio(): string
    {
        $gcd = $this->calculateGCD($this->width, $this->height);
        return ($this->width / $gcd) . ':' . ($this->height / $gcd);
    }

    /**
     * @return int
     */
    private function calculateGCD(int $a, int $b): int
    {
        return $b === 0 ? $a : $this->calculateGCD($b, $a % $b);
    }
}
