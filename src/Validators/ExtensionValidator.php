<?php

namespace Vigihdev\WpCliTools\Validators;

use Vigihdev\WpCliTools\Exceptions\ExtensionException;

final class ExtensionValidator
{

    public static function validate()
    {
        return new self();
    }

    public function mustBeImagick(): self
    {
        if (!extension_loaded('imagick')) {
            throw ExtensionException::notAvailable('imagick');
        }
        return $this;
    }
}
