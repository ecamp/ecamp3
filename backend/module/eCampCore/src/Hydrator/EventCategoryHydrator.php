<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\EventCategory;
use eCamp\Lib\Entity\EntityLink;
use eCamp\Lib\Hydrator\Util;
use Zend\Hydrator\HydratorInterface;

class EventCategoryHydrator implements HydratorInterface {
    public static function HydrateInfo() {
        return [
            'event_type' => Util::Entity(function (EventCategory $ec) {
                return $ec->getEventType();
            }),
        ];
    }

    /**
     * @param object $object
     *
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

            'camp' => EntityLink::Create($eventCategory->getCamp()),
            'event_type' => EntityLink::Create($eventCategory->getEventType()),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var EventCategory $eventCategory */
        $eventCategory = $object;

        if( isset($data['camp']) ) $eventCategory->setCamp($data['camp']);
        if( isset($data['event_type']) ) $eventCategory->setEventType($data['event_type']);

        if( isset($data['short']) ) $eventCategory->setShort($data['short']);
        if( isset($data['name']) ) $eventCategory->setName($data['name']);

        if( isset($data['color']) ) $eventCategory->setColor($data['color']);
        if( isset($data['numbering_style']) ) $eventCategory->setNumberingStyle($data['numbering_style']);

        return $eventCategory;
    }
}
