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
            'eventType' => new EntityLink($eventTypePlugin->getEventType()),
            'plugin' => $eventTypePlugin->getPlugin(),
            'minNumberPluginInstances' => $eventTypePlugin->getMinNumberPluginInstances(),
            'maxNumberPluginInstances' => $eventTypePlugin->getMaxNumberPluginInstances(),
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
