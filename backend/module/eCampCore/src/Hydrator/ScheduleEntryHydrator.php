<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\Day;
use eCamp\Core\Entity\ScheduleEntry;
use eCamp\Lib\Entity\EntityLink;
use eCamp\Lib\Hydrator\Util;
use eCampApi\V1\Rest\ActivityContent\ActivityContentCollection;
use Laminas\Hydrator\HydratorInterface;

class ScheduleEntryHydrator implements HydratorInterface {
    public static function HydrateInfo() {
        return [
            'activity' => Util::Entity(
                function (ScheduleEntry $ei) {
                    return $ei->getActivity();
                },
                [
                    'activityContents' => Util::Collection(function (Activity $e) {
                        return new ActivityContentCollection($e->getActivityContents());
                    }, null),
                ]
            ),
            'period' => Util::Entity(
                function (ScheduleEntry $ei) {
                    return $ei->getPeriod();
                },
            ),
        ];
    }

    /**
     * @param object $object
     *
     * @return array
     */
    public function extract($object) {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = $object;

        $dayNumber = $scheduleEntry->getDayNumber();
        /** @var Day $day */
        $day = $scheduleEntry->getPeriod()->getDays()->filter(function (Day $day) use ($dayNumber) {
            return $day->getDayNumber() === $dayNumber;
        })->first();

        return [
            'id' => $scheduleEntry->getId(),

            'start' => $scheduleEntry->getStart(),
            'length' => $scheduleEntry->getLength(),
            'left' => $scheduleEntry->getLeft(),
            'width' => $scheduleEntry->getWidth(),

            'startTime' => Util::extractDateTime($scheduleEntry->getStartTime()),
            'endTime' => Util::extractDateTime($scheduleEntry->getEndTime()),

            'dayNumber' => $scheduleEntry->getDayNumber(),
            'scheduleEntryNumber' => $scheduleEntry->getScheduleEntryNumber(),
            'number' => $scheduleEntry->getNumber(),

            'activity' => EntityLink::Create($scheduleEntry->getActivity()),
            'period' => EntityLink::Create($scheduleEntry->getPeriod()),
            'day' => EntityLink::Create($day),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = $object;

        if (isset($data['start'])) {
            $scheduleEntry->setStart($data['start']);
        }
        if (isset($data['length'])) {
            $scheduleEntry->setLength($data['length']);
        }
        if (isset($data['left'])) {
            $scheduleEntry->setLeft($data['left']);
        }
        if (isset($data['width'])) {
            $scheduleEntry->setWidth($data['width']);
        }

        return $scheduleEntry;
    }
}
