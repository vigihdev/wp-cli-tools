<?php

declare(strict_types=1);

namespace Vigihdev\WpCliTools\Exceptions\Handler;

use Throwable;

interface HandlerExceptionInterface
{

    public function handle(Throwable $e): void;
}
