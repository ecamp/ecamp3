<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Period;
use eCamp\Lib\Entity\EntityLink;
use eCamp\Lib\Entity\EntityLinkCollection;
use eCamp\Lib\Hydrator\Util;
use eCampApi\V1\Rest\Day\DayCollection;
use eCampApi\V1\Rest\EventCategory\EventCategoryCollection;
use eCampApi\V1\Rest\Period\PeriodCollection;
use Zend\Hydrator\HydratorInterface;

class CampHydrator implements HydratorInterface {

    public static function HydrateInfo() {
        return [
            'camp_type' => Util::Entity(function (Camp $c){
                return $c->getCampType();
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
            }),
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

//            'owner' => EntityLink::Create($camp->getOwner()),
            'creator' => EntityLink::Create($camp->getCreator()),
            'camp_type' => EntityLink::Create($camp->getCampType()),

            'camp_collaborations' => new EntityLinkCollection($camp->getCampCollaborations()),

            'periods' => new EntityLinkCollection($camp->getPeriods()),

            'event_categories' => new EntityLinkCollection($camp->getEventCategories()),
            'events' => new EntityLinkCollection($camp->getEvents()),
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
