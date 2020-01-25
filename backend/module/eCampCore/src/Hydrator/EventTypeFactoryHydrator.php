<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\EventTypeFactory;
use Zend\Hydrator\HydratorInterface;

class EventTypeFactoryHydrator implements HydratorInterface {
    public static function HydrateInfo() {
        return [
        ];
    }

    /**
     * @param object $object
     * @return array
     */
    public function extract($object) {
        /** @var EventTypeFactory $eventTypeFactory */
        $eventTypeFactory = $object;
        return [
            'id' => $eventTypeFactory->getId(),
            'name' => $eventTypeFactory->getName(),
            'event_type' => $eventTypeFactory->getEventType(),
            'factory_name' => $eventTypeFactory->getFactoryName()
        ];
    }

    /**
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var EventTypeFactory $eventTypeFactory */
        $eventTypeFactory = $object;

        return $eventTypeFactory;
    }
}
