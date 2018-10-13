<?php

namespace eCamp\Web\View\Helper\Service;

use Interop\Container\ContainerInterface;
use Zend\View\Helper\Service\AssetFactory;
use Zend\View\Exception\RuntimeException;
use Zend\View\Helper\Asset;

class RobustAssetFactory extends AssetFactory {
    /**
     * {@inheritDoc}
     *
     * @param ContainerInterface $container
     * @param string $name
     * @param null|array $options
     * @return Asset
     * @throws Exception\RuntimeException
     */
    public function __invoke(ContainerInterface $container, $name, array $options = null) {
        try {
            return parent::__invoke($container, $name, $options);
        } catch (RuntimeException $e) {
            // Fail gracefully and leave error messages (if any) to the actual ViewHelper classes
            return new Asset();
        }
    }
}
