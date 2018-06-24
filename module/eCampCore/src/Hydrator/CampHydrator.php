<?php

namespace eCamp\Core\Hydrator;

use eCamp\Api\Collection\CampCollaborationCollection;
use eCamp\Api\Collection\EventCategoryCollection;
use eCamp\Api\Collection\JobCollection;
use eCamp\Api\Collection\PeriodCollection;
use eCamp\Core\Entity\Camp;
use Zend\Hydrator\HydratorInterface;
use ZF\Hal\Link\Link;

class CampHydrator implements HydratorInterface {

    // TODO: Move to Core


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
            'jobs' => new JobCollection($camp->getJobs()),
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

        $camp->setName($data['name']);
        $camp->setTitle($data['title']);
        $camp->setMotto($data['motto']);

        return $camp;
    }
}
