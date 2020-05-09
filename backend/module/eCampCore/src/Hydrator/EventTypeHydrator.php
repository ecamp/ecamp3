<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\EventType;
use eCampApi\V1\Rest\EventTypePlugin\EventTypePluginCollection;
use Zend\Hydrator\HydratorInterface;

class EventTypeHydrator implements HydratorInterface {
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
        /** @var EventType $eventType */
        $eventType = $object;

        return [
            'id' => $eventType->getId(),
            'name' => $eventType->getName(),
            'defaultColor' => $eventType->getDefaultColor(),
            'defaultNumberingStyle' => $eventType->getDefaultNumberingStyle(),
            'eventTypePlugins' => new EventTypePluginCollection($eventType->getEventTypePlugins()),
            //            'eventTypeFactories' => new EventTypeFactoryCollection($eventType->getEventTypeFactories()),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var EventType $eventType */
        $eventType = $object;

        if (isset($data['name'])) {
            $eventType->setName($data['name']);
        }
        if (isset($data['defaultColor'])) {
            $eventType->setDefaultColor($data['defaultColor']);
        }
        if (isset($data['defaultNumberingStyle'])) {
            $eventType->setDefaultNumberingStyle($data['defaultNumberingStyle']);
        }

        return $eventType;
    }
}
