<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Period;
use eCamp\Lib\Entity\EntityLink;
use eCamp\Lib\Entity\EntityLinkCollection;
use eCamp\Lib\Hydrator\Util;
use eCamp\Lib\Types\DateUtc;
use eCampApi\V1\Rest\Day\DayCollection;
use eCampApi\V1\Rest\ScheduleEntry\ScheduleEntryCollection;
use Laminas\ApiTools\Hal\Link\Link;
use Laminas\Hydrator\HydratorInterface;

class PeriodHydrator implements HydratorInterface {
    public static function HydrateInfo(): array {
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
     */
    public function extract($object): array {
        /** @var Period $period */
        $period = $object;

        return [
            'id' => $period->getId(),
            'description' => $period->getDescription(),
            'start' => $period->getStart(),
            'end' => $period->getEnd(),

            'camp' => EntityLink::Create($period->getCamp()),
            'days' => new EntityLinkCollection($period->getDays()),
            'scheduleEntries' => new EntityLinkCollection($period->getScheduleEntries()),

            'materialItems' => Link::factory([
                'rel' => 'materialItems',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.material-item',
                    'options' => ['query' => ['periodId' => $period->getId()]],
                ],
            ]),
        ];
    }

    /**
     * @param object $object
     *
     * @throws \Exception
     */
    public function hydrate(array $data, $object): Period {
        /** @var Period $period */
        $period = $object;

        if (isset($data['start'])) {
            $period->setStart(new DateUtc($data['start']));
        }

        if (isset($data['end'])) {
            $period->setEnd(new DateUtc($data['end']));
        }

        if (isset($data['description'])) {
            $period->setDescription($data['description']);
        }

        return $period;
    }
}
