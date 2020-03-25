<?php

namespace eCamp\Lib\Hal\Factory;

use eCamp\Lib\Hal\Extractor\LinkExtractor;
use ZF\Hal\Link\LinkUrlBuilder;

class LinkExtractorFactory {
    /**
     * @param  \Interop\Container\ContainerInterface|\Zend\ServiceManager\ServiceLocatorInterface $container
     * @return LinkExtractor
     */
    public function __invoke($container) {
        return new LinkExtractor($container->get(LinkUrlBuilder::class));
    }
}