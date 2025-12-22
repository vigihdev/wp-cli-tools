<?php

declare(strict_types=1);

namespace Vigihdev\WpCliTools\Exceptions\Handler;

use Throwable;
use Vigihdev\WpCliTools\Exceptions\WpCliToolsException;

class DefaultExceptionHandler implements HandlerExceptionInterface
{

    public function handle(Throwable $e): void
    {
        if ($e instanceof WpCliToolsException) {
            $this->handleWpCliToolsException($e);
            return;
        }

        echo $e->getMessage();
    }

    private function handleWpCliToolsException(WpCliToolsException $e): void
    {
        echo $e->getMessage();
    }
}
