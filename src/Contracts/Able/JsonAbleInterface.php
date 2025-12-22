<?php

declare(strict_types=1);

namespace Vigihdev\WpCliTools\Contracts\Able;


interface JsonAbleInterface
{
    public function toJson(): string;
}
