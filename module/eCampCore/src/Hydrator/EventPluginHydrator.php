<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\EventPlugin;
use Zend\Hydrator\HydratorInterface;

class EventPluginHydrator implements HydratorInterface
{
    /**
     * @param object $object
     * @return array
     */
    public function extract($object) {
        /** @var EventPlugin $eventPlugin */
        $eventPlugin = $object;
        return [
            'id' => $eventPlugin->getId(),
            'instance_name' => $eventPlugin->getInstanceName(),

            'event' => $eventPlugin->getEvent(),
            'plugin' => $eventPlugin->getPlugin(),
        ];
    }

    /**
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var EventPlugin $eventCategory */
        $eventCategory = $object;

        $eventCategory->setInstanceName($data['instance_name']);

        return $eventCategory;
    }
}