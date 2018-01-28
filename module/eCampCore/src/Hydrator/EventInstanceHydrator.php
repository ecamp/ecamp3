<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\EventInstance;
use eCamp\Lib\Hydrator\Util;
use Zend\Hydrator\HydratorInterface;

class EventInstanceHydrator implements HydratorInterface
{
    /**
     * @param object $object
     * @return array
     */
    public function extract($object) {
        /** @var EventInstance $eventInstance */
        $eventInstance = $object;
        return [
            'id' => $eventInstance->getId(),
            'event' => $eventInstance->getEvent(),

            'camp' => $eventInstance->getCamp(),
            'period' => $eventInstance->getPeriod(),

            'start' => $eventInstance->getStart(),
            'length' => $eventInstance->getLength(),
            'left' => $eventInstance->getLeft(),
            'width' => $eventInstance->getWidth(),

            'start_time' => Util::extractDateTime($eventInstance->getStartTime()),
            'end_time' => Util::extractDateTime($eventInstance->getEndTime()),

            'day_number' => $eventInstance->getDayNumber(),
            'event_instance_number' => $eventInstance->getEventInstanceNumber(),
            'number' => $eventInstance->getNumber(),
        ];
    }

    /**
     * @param array $data
     * @param object $object
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