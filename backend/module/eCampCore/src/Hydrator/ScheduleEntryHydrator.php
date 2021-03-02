<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\Day;
use eCamp\Core\Entity\ScheduleEntry;
use eCamp\Lib\Entity\EntityLink;
use eCamp\Lib\Hydrator\Util;
use eCampApi\V1\Rest\ContentNode\ContentNodeCollection;
use Laminas\Hydrator\HydratorInterface;

class ScheduleEntryHydrator implements HydratorInterface {
    public static function HydrateInfo(): array {
        return [
            'activity' => Util::Entity(
                function (ScheduleEntry $ei) {
                    return $ei->getActivity();
                },
                [
                    'contentNodes' => Util::Collection(function (Activity $e) {
                        return new ContentNodeCollection($e->getAllContentNodes());
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
     */
    public function extract($object): array {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = $object;

        $dayNumber = $scheduleEntry->getDayNumber();
        /** @var Day $day */
        $day = $scheduleEntry->getPeriod()->getDays()->filter(function (Day $day) use ($dayNumber) {
            return $day->getDayNumber() === $dayNumber;
        })->first();

        return [
            'id' => $scheduleEntry->getId(),

            'periodOffset' => $scheduleEntry->getPeriodOffset(),
            'length' => $scheduleEntry->getLength(),
            'left' => $scheduleEntry->getLeft(),
            'width' => $scheduleEntry->getWidth(),

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
     */
    public function hydrate(array $data, $object): ScheduleEntry {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = $object;

        if (isset($data['periodOffset'])) {
            $scheduleEntry->setPeriodOffset($data['periodOffset']);
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
