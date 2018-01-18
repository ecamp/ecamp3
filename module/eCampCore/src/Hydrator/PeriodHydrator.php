<?php

namespace eCamp\Core\Hydrator;

use eCamp\Api\Collection\DayCollection;
use eCamp\Core\Entity\Period;
use Zend\Hydrator\HydratorInterface;

class PeriodHydrator implements HydratorInterface
{
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
            'start' => $period->getStart(),
            'end' => $period->getEnd(),
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

        $period->setDescription($data['description']);
        $period->setStart($data['start']);
        $period->setEnd($data['end']);

        return $period;
    }
}
