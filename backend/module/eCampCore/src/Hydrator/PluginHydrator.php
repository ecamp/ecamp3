<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Plugin;
use Zend\Hydrator\HydratorInterface;

class PluginHydrator implements HydratorInterface {
    public static function HydrateInfo() {
        return [
        ];
    }

    /**
     * @param object $object
     *
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
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var Plugin $plugin */
        $plugin = $object;

        $plugin->setName($data['name']);
        $plugin->setActive($data['active']);

        return $plugin;
    }
}
