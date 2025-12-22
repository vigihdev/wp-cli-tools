<?php

declare(strict_types=1);

namespace Vigihdev\WpCliTools\Contracts\Image;

interface RatioImageInterface
{

    public function getWidth(): int;
    public function getHeight(): int;
    public function getRatio(): float;
    public function getRatioString(): string;
    public function isLandscape(): bool;
    public function isPortrait(): bool;
    public function isSquare(): bool;
    public function getSimplifiedRatio(): string;
}
