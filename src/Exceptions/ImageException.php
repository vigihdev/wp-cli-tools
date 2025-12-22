<?php

declare(strict_types=1);

namespace Vigihdev\WpCliTools\Exceptions;

final class ImageException extends WpCliToolsException
{
    public const INVALID_DIMENSIONS   = 2001;
    public const UNSUPPORTED_FORMAT   = 2002;
    public const PROCESSING_FAILED    = 2003;
    public const LIBRARY_NOT_AVAILABLE = 2004;
    public const INVALID_RATIO        = 2005;
    public const SCALE_OUT_OF_BOUNDS  = 2006;

    public static function invalidDimensions(int $width, int $height): self
    {
        return new self(
            message: sprintf("Invalid image dimensions: %dx%d", $width, $height),
            code: self::INVALID_DIMENSIONS,
            context: ['width' => $width, 'height' => $height],
            solutions: [
                "Check if the image file is corrupted",
                "Verify the image has valid width and height",
                "Try opening and resaving the image in an editor"
            ]
        );
    }

    public static function unsupportedFormat(string $format): self
    {
        return new self(
            message: sprintf("Unsupported image format: %s", $format),
            code: self::UNSUPPORTED_FORMAT,
            context: [
                'format' => $format,
                'supported_formats' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']
            ],
            solutions: [
                "Convert the image to a supported format (JPEG, PNG, WebP, etc.)",
                "Use an image editing tool to reformat the file",
                "Check if the file extension matches its actual format"
            ]
        );
    }

    public static function processingFailed(string $reason = ''): self
    {
        $message = "Image processing failed";
        if ($reason !== '') {
            $message .= ": " . $reason;
        }

        return new self(
            message: $message,
            code: self::PROCESSING_FAILED,
            context: $reason !== '' ? ['reason' => $reason] : [],
            solutions: [
                "Check server memory limits (increase if necessary)",
                "Verify the image is not corrupted",
                "Try processing with a different image library (Imagick vs GD)",
                "Check error logs for more details"
            ]
        );
    }

    public static function libraryNotAvailable(string $library): self
    {
        return new self(
            message: sprintf("Image library not available: %s", $library),
            code: self::LIBRARY_NOT_AVAILABLE,
            context: [
                'library' => $library,
                'required_extensions' => [
                    'imagick' => 'imagick',
                    'gd' => 'gd',
                    'gmagick' => 'gmagick'
                ]
            ],
            solutions: [
                sprintf("Install the %s PHP extension", $library),
                "Enable the extension in your php.ini file",
                "Run phpinfo() to verify the extension is loaded",
                "Restart your web server/PHP-FPM after installation"
            ]
        );
    }

    public static function invalidRatio(float $ratio): self
    {
        return new self(
            message: sprintf("Invalid aspect ratio: %.2f", $ratio),
            code: self::INVALID_RATIO,
            context: ['ratio' => $ratio],
            solutions: [
                "Ensure the calculated ratio is greater than 0",
                "Check if width or height is zero or negative",
                "Verify the image dimensions are valid before calculating ratio"
            ]
        );
    }

    public static function scaleOutOfBounds(float $percentage, float $min = 0.1, float $max = 500): self
    {
        return new self(
            message: sprintf(
                "Scale percentage (%.1f%%) must be between %.1f%% and %.1f%%",
                $percentage,
                $min * 100,
                $max * 100
            ),
            code: self::SCALE_OUT_OF_BOUNDS,
            context: [
                'percentage' => $percentage,
                'min_allowed' => $min * 100,
                'max_allowed' => $max * 100
            ],
            solutions: [
                "Use a scale percentage between 0.1% and 500%",
                "Consider using absolute pixel dimensions instead of percentage",
                "Adjust your scaling factor to stay within allowed bounds"
            ]
        );
    }
}
