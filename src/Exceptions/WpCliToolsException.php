<?php

declare(strict_types=1);

namespace Vigihdev\WpCliTools\Exceptions;

abstract class WpCliToolsException extends \RuntimeException
{
    protected array $context = [];
    protected array $solutions = [];

    public function __construct(
        string $message,
        int $code = 0,
        \Throwable $previous = null,
        array $context = [],
    ) {
        $this->context = $context;
        parent::__construct($message, $code, $previous);
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function withContext(array $context): self
    {
        $this->context = array_merge($this->context, $context);
        return $this;
    }
}
