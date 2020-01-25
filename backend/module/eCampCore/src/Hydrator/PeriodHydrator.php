<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Period;
use eCamp\Lib\Hydrator\Util;
use eCampApi\V1\Rest\Day\DayCollection;
use Zend\Hydrator\HydratorInterface;

class PeriodHydrator implements HydratorInterface {

    public static function HydrateInfo() {
        return [
            'camp' => Util::Entity(function (Period $p) {
                return $p->getCamp();
            }),
            'days' => Util::Collection(function (Period $p) {
                return new DayCollection($p->getDays());
            }),
        ];
    }

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
//            'camp' => $period->getCamp(),
//            'days' => new DayCollection($period->getDays()),
//            'event_instances' => new EventInstanceCollection($period->getEventInstances())
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
