<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Day;
use eCamp\Lib\Hydrator\Util;
use Zend\Hydrator\HydratorInterface;

class DayHydrator implements HydratorInterface {

    public static function HydrateInfo() {
        return [
            'period' => Util::Entity(function (Day $d) {
                return $d->getPeriod();
            }),
            'camp' => Util::EntityLink(function (Day $d) {
                return $d->getPeriod()->getCamp();
            })
        ];
    }

    /**
     * @param object $object
     * @return array
     */
    public function extract($object) {
        /** @var Day $day */
        $day = $object;
        return [
            'id' => $day->getId(),
            'day_offset' => $day->getDayOffset(),
            'number' => $day->getDayNumber(),
        ];
    }

    /**
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var Day $day */
        $day = $object;

        $day->setDayOffset($data['day_offset']);

        return $day;
    }
}
