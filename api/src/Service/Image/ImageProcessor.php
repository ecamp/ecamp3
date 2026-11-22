<?php

namespace App\Service\Image;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final readonly class ImageProcessor {
    public function __construct(
        private int $maxBytes,
        private int $maxPixels,
        private int $maxDimension,
        private int $quality,
        private SvgRasterizer $svgRasterizer = new SvgRasterizer(),
    ) {
        if ($maxBytes < 1 || $maxPixels < 1 || $maxDimension < 1 || $quality < 1 || $quality > 100) {
            throw new \InvalidArgumentException('Invalid image processing limits.');
        }
    }

    public function process(UploadedFile $file): ProcessedImage {
        if (!$file->isValid() || $file->getSize() > $this->maxBytes) {
            throw new UnprocessableEntityHttpException('The image upload failed or exceeds the maximum file size.');
        }

        $path = $file->getPathname();
        $data = file_get_contents($path);
        if (false === $data || '' === $data) {
            throw new UnprocessableEntityHttpException('The image could not be read.');
        }
        $info = @getimagesizefromstring($data);
        if (false === $info || 'image/svg+xml' === $info['mime']) {
            $dimension = min($this->maxDimension, (int) floor(sqrt($this->maxPixels)));
            $data = $this->svgRasterizer->rasterize($data, $dimension);
            $info = @getimagesizefromstring($data);
        }
        if (false === $info || !in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP, IMAGETYPE_AVIF, IMAGETYPE_GIF, IMAGETYPE_BMP], true)) {
            throw new UnprocessableEntityHttpException('Only JPEG, PNG, WebP, AVIF, GIF, BMP and SVG images are supported.');
        }
        if ($info[0] < 1 || $info[1] < 1 || $info[0] > $this->maxPixels / $info[1]) {
            throw new UnprocessableEntityHttpException('The image exceeds the maximum pixel count.');
        }

        $memoryLimit = ini_parse_quantity(ini_get('memory_limit'));
        $estimatedMemory = $info[0] * $info[1] * 12 + strlen($data) * 2 + 16 * 1024 * 1024;
        if ($memoryLimit > 0 && memory_get_usage(true) + $estimatedMemory > $memoryLimit) {
            throw new UnprocessableEntityHttpException('The image is too large to process.');
        }

        $image = @imagecreatefromstring($data);
        if (false === $image) {
            throw new UnprocessableEntityHttpException('The image could not be decoded.');
        }

        if (IMAGETYPE_JPEG === $info[2]) {
            $exif = @exif_read_data($path);
            $image = $this->orient($image, (int) ($exif['Orientation'] ?? 1));
        }

        $scale = min(1, $this->maxDimension / max(imagesx($image), imagesy($image)));
        $width = max(1, (int) round(imagesx($image) * $scale));
        $height = max(1, (int) round(imagesy($image) * $scale));
        $output = imagecreatetruecolor($width, $height);
        imagealphablending($output, false);
        imagesavealpha($output, true);
        imagecopyresampled($output, $image, 0, 0, 0, 0, $width, $height, imagesx($image), imagesy($image));

        ob_start();

        try {
            $success = imageavif($output, null, $this->quality);
            $encoded = ob_get_contents();
        } finally {
            ob_end_clean();
        }
        if (!$success || false === $encoded || '' === $encoded) {
            throw new \RuntimeException('The image could not be encoded.');
        }

        return new ProcessedImage($encoded, $width, $height);
    }

    private function orient(\GdImage $image, int $orientation): \GdImage {
        if (in_array($orientation, [2, 5, 7], true)) {
            imageflip($image, IMG_FLIP_HORIZONTAL);
        } elseif (4 === $orientation) {
            imageflip($image, IMG_FLIP_VERTICAL);
        }

        $angle = match ($orientation) {
            3 => 180,
            5, 8 => 90,
            6, 7 => -90,
            default => 0,
        };
        if (0 === $angle) {
            return $image;
        }

        $rotated = imagerotate($image, $angle, 0);
        if (false === $rotated) {
            throw new \RuntimeException('The image could not be rotated.');
        }

        return $rotated;
    }
}
