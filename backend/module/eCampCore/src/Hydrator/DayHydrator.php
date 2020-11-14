<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Day;
use eCamp\Lib\Entity\EntityLink;
use eCamp\Lib\Entity\EntityLinkCollection;
use Laminas\Hydrator\HydratorInterface;

class DayHydrator implements HydratorInterface {
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
        /** @var Day $day */
        $day = $object;

        return [
            'id' => $day->getId(),
            'dayOffset' => $day->getDayOffset(),
            'number' => $day->getDayNumber(),

            'period' => EntityLink::Create($day->getPeriod()),

            'scheduleEntries' => new EntityLinkCollection($day->getScheduleEntries()),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var Day $day */
        $day = $object;

        if (isset($data['dayOffset'])) {
            $day->setDayOffset($data['dayOffset']);
        }

        return $day;
    }
}
