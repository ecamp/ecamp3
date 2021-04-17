<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Day;
use eCamp\Core\Entity\DayResponsible;
use eCamp\Lib\Entity\EntityLink;
use eCamp\Lib\Entity\EntityLinkCollection;
use eCamp\Lib\Hydrator\Util;
use eCampApi\V1\Rest\CampCollaboration\CampCollaborationCollection;
use Laminas\Hydrator\HydratorInterface;

class DayHydrator implements HydratorInterface {
    public static function HydrateInfo(): array {
        return [
            'dayResponsibles' => Util::Collection(function (Day $d) {
                return new CampCollaborationCollection(
                    $d->getDayResponsibles()->map(function (DayResponsible $ar) {
                        return $ar->getCampCollaboration();
                    })
                );
            }, null),
        ];
    }

    /**
     * @param object $object
     */
    public function extract($object): array {
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
     */
    public function hydrate(array $data, $object): Day {
        /** @var Day $day */
        $day = $object;

        if (isset($data['dayOffset'])) {
            $day->setDayOffset($data['dayOffset']);
        }

        return $day;
    }
}
