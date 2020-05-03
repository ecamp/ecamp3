<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Day;
use eCamp\Core\Entity\Event;
use eCamp\Core\Entity\EventInstance;
use eCamp\Lib\Entity\EntityLink;
use eCamp\Lib\Hydrator\Util;
use eCampApi\V1\Rest\EventPlugin\EventPluginCollection;
use Zend\Hydrator\HydratorInterface;

class EventInstanceHydrator implements HydratorInterface {
    public static function HydrateInfo() {
        return [
            'event' => Util::Entity(
                function (EventInstance $ei) {
                    return $ei->getEvent();
                },
                [
                    'event_plugins' => Util::Collection(function (Event $e) {
                        return new EventPluginCollection($e->getEventPlugins());
                    }, null),
                ]
            ),
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

        if (isset($data['start'])) {
            $eventInstance->setStart($data['start']);
        }
        if (isset($data['length'])) {
            $eventInstance->setLength($data['length']);
        }
        if (isset($data['left'])) {
            $eventInstance->setLeft($data['left']);
        }
        if (isset($data['width'])) {
            $eventInstance->setWidth($data['width']);
        }

        return $eventInstance;
    }
}
