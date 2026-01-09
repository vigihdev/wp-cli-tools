<?php

declare(strict_types=1);

namespace Vigihdev\WpCliTools\Exceptions;

final class UriException extends WpCliToolsException
{
    public const INVALID_URI = 4001;
    public const INVALID_SCHEME = 4002;
    public const INVALID_HOST = 4003;
    public const INVALID_PATH = 4004;
    public const INVALID_QUERY = 4005;
    public const INVALID_FRAGMENT = 4006;
    public const UNSUPPORTED_SCHEME = 4007;
    public const MALFORMED_URI = 4008;
    public const NOT_FOUND = 4009;

    public static function notFound(string $uri, int $statusCode = 0): self
    {
        $message = $statusCode
            ? sprintf("URI not found: %s (Status Code: %d)", $uri, $statusCode)
            : sprintf("URI not found: %s", $uri);

        return new self(
            message: $message,
            code: self::NOT_FOUND,
            previous: null,
            context: [
                'uri' => $uri,
                'status_code' => $statusCode,
            ],
            solutions: [
                "Check if the URI is properly formatted",
                "Ensure special characters are URL-encoded",
                "Verify the URI follows RFC 3986 standards"
            ]
        );
    }

    public static function invalid(string $uri, string $reason = ''): self
    {
        $message = $reason
            ? sprintf("Invalid URI '%s': %s", $uri, $reason)
            : sprintf("Invalid URI: %s", $uri);

        return new self(
            message: $message,
            code: self::INVALID_URI,
            previous: null,
            context: [
                'uri' => $uri,
                'reason' => $reason,
                'filtered' => filter_var($uri, FILTER_VALIDATE_URL)
            ],
            solutions: [
                "Check if the URI is properly formatted",
                "Ensure special characters are URL-encoded",
                "Verify the URI follows RFC 3986 standards"
            ]
        );
    }

    public static function invalidScheme(string $uri, string $scheme, array $allowed = []): self
    {
        return new self(
            message: sprintf("Invalid scheme '%s' for URI: %s", $scheme, $uri),
            code: self::INVALID_SCHEME,
            previous: null,
            context: [
                'uri' => $uri,
                'scheme' => $scheme,
                'allowed_schemes' => $allowed,
                'common_schemes' => ['http', 'https', 'ftp', 'file', 'data']
            ],
            solutions: [
                "Check if the URI is properly formatted",
                "Ensure special characters are URL-encoded",
                "Verify the URI follows RFC 3986 standards"
            ]
        );
    }

    public static function unsupportedScheme(string $uri, string $scheme): self
    {
        return new self(
            message: sprintf("Unsupported scheme '%s' for URI: %s", $scheme, $uri),
            code: self::UNSUPPORTED_SCHEME,
            previous: null,
            context: [
                'uri' => $uri,
                'scheme' => $scheme,
                'common_schemes' => ['http', 'https', 'ftp', 'file', 'data']
            ],
            solutions: [
                "Check if the URI is properly formatted",
                "Ensure special characters are URL-encoded",
                "Verify the URI follows RFC 3986 standards"
            ]
        );
    }

    public static function malformed(string $uri, string $component): self
    {
        return new self(
            message: sprintf("Malformed URI component '%s' in: %s", $component, $uri),
            code: self::MALFORMED_URI,
            previous: null,
            context: [
                'uri' => $uri,
                'component' => $component
            ],
            solutions: [
                "Check if the URI is properly formatted",
                "Ensure special characters are URL-encoded",
                "Verify the URI follows RFC 3986 standards"
            ]
        );
    }
}
