<?php

namespace App\Service\Image;

use Dom\HTMLDocument;

final class HtmlImageReferences {
    /** @return array<string, string> */
    public function extract(string $html): array {
        $references = [];
        if ('' === $html) {
            return $references;
        }
        $document = HTMLDocument::createFromString($html, LIBXML_NOERROR, 'UTF-8');
        foreach ($document->getElementsByTagName('img') as $image) {
            if (preg_match('~^/(?:api/)?camps/([0-9a-f]{12,16})/images/([0-9a-f]{12,16})$~D', $image->getAttribute('src'), $match)) {
                $references[$match[2]] = $match[1];
            }
        }

        return $references;
    }
}
