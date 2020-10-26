<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\ActivityResponsible;
use eCamp\Lib\Entity\EntityLink;
use Laminas\Hydrator\HydratorInterface;

class ActivityResponsibleHydrator implements HydratorInterface {
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
        /** @var ActivityResponsible $activityResponsible */
        $activityResponsible = $object;

        return [
            'id' => $activityResponsible->getId(),
            'activity' => EntityLink::Create($activityResponsible->getActivity()),
            'campCollaboration' => EntityLink::Create($activityResponsible->getCampCollaboration()),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        return $object;
    }
}
