<?php

namespace eCamp\Core\Hydrator;

use eCamp\Api\Collection\DayCollection;
use eCamp\Core\Entity\Period;
use eCamp\Lib\Hydrator\Util;
use Zend\Hydrator\HydratorInterface;

class PeriodHydrator implements HydratorInterface {
    /**
     * @param object $object
     * @return array
     */
    public function extract($object) {
        /** @var Period $period */
        $period = $object;
        return [
            'id' => $period->getId(),
            'description' => $period->getDescription(),
            'start' => Util::extractDate($period->getStart()),
            'end' => Util::extractDate($period->getEnd()),
            'camp' => $period->getCamp(),
            'days' => new DayCollection($period->getDays())
        ];
    }

    /**
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var Period $period */
        $period = $object;

        $start = Util::parseDate($data['start']);
        $end = Util::parseDate($data['end']);

        $period->setDescription($data['description']);
        $period->setStart($start);
        $period->setEnd($end);

        return $period;
    }
}
