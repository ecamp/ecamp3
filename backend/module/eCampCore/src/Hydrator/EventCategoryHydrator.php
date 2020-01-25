<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\EventCategory;
use eCamp\Lib\Hydrator\Util;
use Zend\Hydrator\HydratorInterface;

class EventCategoryHydrator implements HydratorInterface {

    public static function HydrateInfo() {
        return [
            'camp' => Util::Entity(function (EventCategory $ec) {
                return $ec->getCamp();
            }),
            'event_type' => Util::Entity(function (EventCategory $ec) {
                return $ec->getEventType();
            })
        ];
    }

    /**
     * @param object $object
     * @return array
     */
    public function extract($object) {
        /** @var EventCategory $eventCategory */
        $eventCategory = $object;
        return [
            'id' => $eventCategory->getId(),
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
