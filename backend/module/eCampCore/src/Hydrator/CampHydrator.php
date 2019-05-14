<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Camp;
use eCampApi\V1\Rest\CampCollaboration\CampCollaborationCollection;
use eCampApi\V1\Rest\EventCategory\EventCategoryCollection;
use eCampApi\V1\Rest\Period\PeriodCollection;
use Zend\Hydrator\HydratorInterface;

class CampHydrator implements HydratorInterface {

    /**
     * @param object $object
     * @return array
     */
    public function extract($object) {
        /** @var Camp $camp */
        $camp = $object;
        return [
            'id' => $camp->getId(),
            'name' => $camp->getName(),
            'title' => $camp->getTitle(),
            'motto' => $camp->getMotto(),
            'camp_type' => $camp->getCampType(),
            'owner' => $camp->getOwner(),
            'creator' => $camp->getCreator(),

            'camp_collaborations' => new CampCollaborationCollection($camp->getCampCollaborations()),
//            'jobs' => new JobCollection($camp->getJobs()),
            'periods' => new PeriodCollection($camp->getPeriods()),
            'event_categories' => new EventCategoryCollection($camp->getEventCategories()),

        ];
    }

    /**
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var Camp $camp */
        $camp = $object;

        $camp->setTitle($data['title']);
        $camp->setMotto($data['motto']);

        return $camp;
    }
}
