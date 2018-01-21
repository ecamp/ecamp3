<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Plugin;
use Zend\Hydrator\HydratorInterface;

class PluginHydrator implements HydratorInterface
{
    /**
     * @param object $object
     * @return array
     */
    public function extract($object) {
        /** @var Plugin $plugin */
        $plugin = $object;
        return [
            'id' => $plugin->getId(),
            'name' => $plugin->getName(),
            'active' => $plugin->getActive(),
        ];
    }

    /**
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var Plugin $plugin */
        $plugin = $object;

        return $plugin;
    }
}