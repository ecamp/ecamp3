<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\EventCategory;
use eCamp\Lib\Entity\EntityLink;
use eCamp\Lib\Hydrator\Util;
use Zend\Hydrator\HydratorInterface;

class EventCategoryHydrator implements HydratorInterface {
    public static function HydrateInfo() {
        return [
            'eventType' => Util::Entity(function (EventCategory $ec) {
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
            'numberingStyle' => $eventCategory->getNumberingStyle(),

            'camp' => EntityLink::Create($eventCategory->getCamp()),
            'eventType' => EntityLink::Create($eventCategory->getEventType()),
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

        if (isset($data['camp'])) {
            $eventCategory->setCamp($data['camp']);
        }
        if (isset($data['eventType'])) {
            $eventCategory->setEventType($data['eventType']);
        }

        if (isset($data['short'])) {
            $eventCategory->setShort($data['short']);
        }
        if (isset($data['name'])) {
            $eventCategory->setName($data['name']);
        }

        if (isset($data['color'])) {
            $eventCategory->setColor($data['color']);
        }
        if (isset($data['numberingStyle'])) {
            $eventCategory->setNumberingStyle($data['numberingStyle']);
        }

        return $eventCategory;
    }
}
