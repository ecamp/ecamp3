<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\EventTypePlugin;
use eCamp\Lib\Entity\EntityLink;
use Zend\Hydrator\HydratorInterface;

class EventTypePluginHydrator implements HydratorInterface {
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
        /** @var EventTypePlugin $eventTypePlugin */
        $eventTypePlugin = $object;

        return [
            'id' => $eventTypePlugin->getId(),
            'event_type' => new EntityLink($eventTypePlugin->getEventType()),
            'plugin' => $eventTypePlugin->getPlugin(),
            'min_number_plugin_instances' => $eventTypePlugin->getMinNumberPluginInstances(),
            'max_number_plugin_instances' => $eventTypePlugin->getMaxNumberPluginInstances(),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        // @var EventTypePlugin $eventTypePlugin
        return $object;
    }
}
