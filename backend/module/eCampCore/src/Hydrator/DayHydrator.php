<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Day;
use Zend\Hydrator\HydratorInterface;
use ZF\Hal\Link\Link;

class DayHydrator implements HydratorInterface {
    /**
     * @param object $object
     * @return array
     */
    public function extract($object) {
        /** @var Day $day */
        $day = $object;
        return [
            'id' => $day->getId(),
            'period' => $day->getPeriod(),
            'day_offset' => $day->getDayOffset(),
            'number' => $day->getDayNumber(),

            'camp' => Link::factory([
                'rel' => 'camp',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.camp',
                    'params' => [ 'camp_id' => $day->getCamp()->getId() ]
                ]
            ]),
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
