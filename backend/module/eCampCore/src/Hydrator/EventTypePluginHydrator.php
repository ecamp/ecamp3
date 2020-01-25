<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\EventTypePlugin;
use Zend\Hydrator\HydratorInterface;

class EventTypePluginHydrator implements HydratorInterface {

    public static function HydrateInfo() {
        return [
        ];
    }

    /**
     * @param object $object
     * @return array
     */
    public function extract($object) {
        /** @var EventTypePlugin $eventTypePlugin */
        $eventTypePlugin = $object;
        return [
            'id' => $eventTypePlugin->getId(),
//            'event_type' => $eventTypePlugin->getEventType(),
//            'plugin' => $eventTypePlugin->getPlugin(),
            'min_number_plugin_instances' => $eventTypePlugin->getMinNumberPluginInstances(),
            'max_number_plugin_instances' => $eventTypePlugin->getMaxNumberPluginInstances(),

        ];
    }

    /**
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var EventTypePlugin $eventTypePlugin */
        $eventTypePlugin = $object;

        return $eventTypePlugin;
    }
}
