<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\EventCategory;
use Zend\Hydrator\HydratorInterface;

class EventCategoryHydrator implements HydratorInterface {
    /**
     * @param object $object
     * @return array
     */
    public function extract($object) {
        /** @var EventCategory $eventCategory */
        $eventCategory = $object;
        return [
            'id' => $eventCategory->getId(),
            'camp' => $eventCategory->getCamp(),
            'event_type' => $eventCategory->getEventType(),
            'short' => $eventCategory->getShort(),
            'name' => $eventCategory->getName(),

            'color' => $eventCategory->getColor(),
            'numbering_style' => $eventCategory->getNumberingStyle(),
        ];
    }

    /**
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var EventCategory $eventCategory */
        $eventCategory = $object;

        $eventCategory->setCamp($data['camp']);
        $eventCategory->setEventType($data['event_type']);

        $eventCategory->setShort($data['short']);
        $eventCategory->setName($data['name']);

        $eventCategory->setColor($data['color']);
        $eventCategory->setNumberingStyle($data['numbering_style']);

        return $eventCategory;
    }
}
