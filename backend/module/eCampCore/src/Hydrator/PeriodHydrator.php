<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Period;
use eCamp\Core\Types\DateUTC;
use eCamp\Lib\Entity\EntityLink;
use eCamp\Lib\Entity\EntityLinkCollection;
use eCamp\Lib\Hydrator\Util;
use eCampApi\V1\Rest\Day\DayCollection;
use eCampApi\V1\Rest\ScheduleEntry\ScheduleEntryCollection;
use Laminas\Hydrator\HydratorInterface;

class PeriodHydrator implements HydratorInterface {
    public static function HydrateInfo() {
        return [
            'days' => Util::Collection(function (Period $p) {
                return new DayCollection($p->getDays());
            }, null),
            'scheduleEntries' => Util::Collection(function (Period $p) {
                return new ScheduleEntryCollection($p->getScheduleEntries());
            }, null),
        ];
    }

    /**
     * @param object $object
     *
     * @return array
     */
    public function extract($object) {
        /** @var Period $period */
        $period = $object;

        return [
            'id' => $period->getId(),
            'description' => $period->getDescription(),
            'start' => $period->getStart()->__toString(),
            'end' => $period->getEnd()->__toString(),

            'camp' => EntityLink::Create($period->getCamp()),
            'days' => new EntityLinkCollection($period->getDays()),
            'scheduleEntries' => new EntityLinkCollection($period->getScheduleEntries()),
        ];
    }

    /**
     * @param object $object
     *
     * @throws \Exception
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var Period $period */
        $period = $object;

        if (isset($data['start'])) {
            $period->setStart(new DateUTC($data['start']));
        }

        if (isset($data['end'])) {
            $period->setEnd(new DateUTC($data['end']));
        }

        if (isset($data['description'])) {
            $period->setDescription($data['description']);
        }

        return $period;
    }
}
