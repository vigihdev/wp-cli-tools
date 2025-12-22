<?php

declare(strict_types=1);

namespace Vigihdev\WpCliTools\Exceptions;

final class ExtensionException extends WpCliToolsException
{

    public static function notAvailable(string $extension): self
    {
        return new self(
            message: sprintf("Extension %s is not available", $extension),
        );
    }
}
