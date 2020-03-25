<?php

namespace eCamp\Lib\Hal\Extractor;

use ZF\Hal\Extractor\LinkExtractor as HalLinkExtractor;
use ZF\Hal\Link\Link;

class LinkExtractor extends HalLinkExtractor {
    public function extract(Link $object) {
        $representation = parent::extract($object);

        if ($object->isTemplated()) {
            $representation['templated'] = true;
        }

        return $representation;
    }
}