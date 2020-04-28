<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Day;
use eCamp\Core\Entity\EventInstance;
use eCamp\Lib\Entity\EntityLink;
use eCamp\Lib\Hydrator\Util;
use Zend\Hydrator\HydratorInterface;

class EventInstanceHydrator implements HydratorInterface {
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
        /** @var EventInstance $eventInstance */
        $eventInstance = $object;

        $dayNumber = $eventInstance->getDayNumber();
        /** @var Day $day */
        $day = $eventInstance->getPeriod()->getDays()->filter(function (Day $day) use ($dayNumber) {
            return $day->getDayNumber() === $dayNumber;
        })->first();

        return [
            'id' => $eventInstance->getId(),

            'start' => $eventInstance->getStart(),
            'length' => $eventInstance->getLength(),
            'left' => $eventInstance->getLeft(),
            'width' => $eventInstance->getWidth(),

            'start_time' => Util::extractDateTime($eventInstance->getStartTime()),
            'end_time' => Util::extractDateTime($eventInstance->getEndTime()),

            'day_number' => $eventInstance->getDayNumber(),
            'event_instance_number' => $eventInstance->getEventInstanceNumber(),
            'number' => $eventInstance->getNumber(),

            'event' => EntityLink::Create($eventInstance->getEvent()),
            'period' => EntityLink::Create($eventInstance->getPeriod()),
            'day' => EntityLink::Create($day),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var EventInstance $eventInstance */
        $eventInstance = $object;

        $eventInstance->setStart($data['start']);
        $eventInstance->setLength($data['length']);
        $eventInstance->setLeft($data['left']);
        $eventInstance->setWidth($data['width']);

        return $eventInstance;
    }
}
