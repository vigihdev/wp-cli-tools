<?php

declare(strict_types=1);

namespace Vigihdev\WpCliTools\Exceptions\Handler;

use Throwable;
use WP_CLI;
use Vigihdev\WpCliTools\Exceptions\WpCliToolsException;

class DefaultExceptionHandler implements HandlerExceptionInterface
{

    /**
     * Handle the exception.
     * 
     * @param Throwable $e The exception to handle.
     * @return void
     */
    public function handle(Throwable $e): void
    {

        if ($e instanceof WpCliToolsException) {
            $this->handleWpCliToolsException($e);
            return;
        }

        print($e->getMessage());
    }

    /**
     * Handle WpCliToolsException.
     * 
     * @param WpCliToolsException $e The exception to handle.
     * @return void
     */
    private function handleWpCliToolsException(WpCliToolsException $e): void
    {

        $contexts = $e->getContext();
        $solutions = $e->getSolutions();
        $message = sprintf("❌ %s", $e->getMessage());

        $paddingLeft = str_repeat(' ', 5);

        // Jika ada konteks, tambahkan ke pesan
        if (is_array($contexts) && count($contexts) > 0) {
            $maxLabelLength = max(array_map('strlen', array_keys($contexts)));
            foreach ($contexts as $key => $value) {
                $value = is_array($value) ? implode(', ', $value) : (string) $value;
                $value = WP_CLI::colorize("%b{$value}%n");
                $padding = str_repeat(' ', $maxLabelLength - strlen($key));
                $message .= "\n";
                $message .= sprintf("%s%s:%s %s", $paddingLeft, $key, $padding, $value);
            }
        }

        // Jika ada solusi, tambahkan ke pesan
        if (is_array($solutions) && count($solutions) > 0) {
            $message .= "\n\n";
            $message .= sprintf("%s%s", $paddingLeft, WP_CLI::colorize("%GSaran:%n"));
            foreach ($solutions as $solution) {
                $message .= "\n";
                $message .= sprintf("%s   %s", $paddingLeft, WP_CLI::colorize("%g{$solution}%n"));
            }
        }

        WP_CLI::error($message);
    }
}
