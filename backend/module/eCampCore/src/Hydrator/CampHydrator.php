<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Period;
use eCamp\Lib\Hydrator\Util;
use eCampApi\V1\Rest\Day\DayCollection;
use eCampApi\V1\Rest\EventCategory\EventCategoryCollection;
use eCampApi\V1\Rest\Period\PeriodCollection;
use Zend\Hydrator\HydratorInterface;

class CampHydrator implements HydratorInterface {

    public static function HydrateInfo() {
        return [
            'creator' => Util::Entity(function (Camp $c) {
                return $c->getCreator();
            }),
            'periods' => Util::Collection(function (Camp $c) {
                return new PeriodCollection($c->getPeriods());
            }, [
                'days' => Util::Collection(function (Period $p) {
                    return new DayCollection($p->getDays());
                })
            ]),
            'eventCategories' => Util::Collection(function (Camp $c) {
                return new EventCategoryCollection($c->getEventCategories());
            }, []),
            'events' => Util::CollectionLink(function (Camp $c) {
                return $c->getEvents();
            })
        ];
    }

    /**
     * @param object $object
     * @return array
     * @throws \Exception
     */
    public function extract($object) {
        /** @var Camp $camp */
        $camp = $object;
        return [
            'id' => $camp->getId(),
            'name' => $camp->getName(),
            'title' => $camp->getTitle(),
            'motto' => $camp->getMotto(),
//            'camp_type' => $camp->getCampType(),
//            'owner' =>  $camp->getOwner(),
//
//            'creator' => EntityLink::Create($camp->getCreator()),
//
//            'camp_collaborations' => new CampCollaborationCollection($camp->getCampCollaborations()),
//            'jobs' => new JobCollection($camp->getJobs()),
//            'periods' => new PeriodCollection($camp->getPeriods()),
//            'event_categories' => new EventCategoryCollection($camp->getEventCategories()),
//
//            'events' => Link::factory([
//                'rel' => 'events',
//                'route' => [
//                    'name' => 'e-camp-api.rest.doctrine.event',
//                    'options' => ['query' => ['camp_id' => $camp->getId()]]
//                ]
//            ])

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
