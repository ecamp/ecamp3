<?php

namespace App\Service\Image;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

final class SvgRasterizer {
    public function rasterize(string $data, int $maxDimension): string {
        $document = new \DOMDocument();
        if (!@$document->loadXML($data, LIBXML_NONET) || null !== $document->doctype
            || 'svg' !== $document->documentElement?->localName
            || 'http://www.w3.org/2000/svg' !== $document->documentElement->namespaceURI) {
            throw new UnprocessableEntityHttpException('Only JPEG, PNG, WebP, AVIF, GIF, BMP and SVG images are supported.');
        }

        $process = new Process([
            'prlimit', '--as=536870912', '--cpu=10', '--',
            'rsvg-convert', '--format=png', '--keep-aspect-ratio',
            '--width='.$maxDimension, '--height='.$maxDimension,
        ], input: $data, timeout: 10);

        try {
            $process->run();
        } catch (ProcessTimedOutException $exception) {
            throw new UnprocessableEntityHttpException('The SVG image took too long to render.', $exception);
        }
        if (!$process->isSuccessful()) {
            throw new UnprocessableEntityHttpException('The SVG image could not be rendered.');
        }

        return $process->getOutput();
    }
}
