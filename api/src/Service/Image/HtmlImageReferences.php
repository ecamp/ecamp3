<?php

namespace App\Service\Image;

use Dom\HTMLDocument;

final class HtmlImageReferences {
    /** @return list<string> */
    public function extract(string $html): array {
        $references = [];
        if ('' === $html) {
            return $references;
        }
        $document = HTMLDocument::createFromString($html, LIBXML_NOERROR, 'UTF-8');
        foreach ($document->getElementsByTagName('img') as $image) {
            $hash = self::hashFromPath($image->getAttribute('src'));
            if (null !== $hash) {
                $references[] = $hash;
            }
        }

        return $references;
    }

    public static function hashFromPath(string $path): ?string {
        return preg_match('~^/(?:api/)?images/([0-9a-f]{64})/content$~D', $path, $match) ? $match[1] : null;
    }
}
