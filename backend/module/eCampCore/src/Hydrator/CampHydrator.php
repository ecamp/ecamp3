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
use Zend\Authentication\AuthenticationService;
use Zend\Hydrator\HydratorInterface;
use ZF\Hal\Link\Link;

class CampHydrator implements HydratorInterface {
    public static function HydrateInfo() {
        return [
            'camp_type' => Util::Entity(function (Camp $c) {
                return $c->getCampType();
            }),
            'periods' => Util::Collection(
                function (Camp $c) {
                    return new PeriodCollection($c->getPeriods());
                },
                function (Camp $c) {
                    return [
                        Link::factory([
                            'rel' => 'camp_collaborations',
                            'route' => [
                                'name' => 'e-camp-api.rest.doctrine.camp-collaboration',
                                'options' => ['query' => ['camp_id' => $c->getId()]]
                            ]
                        ])
                    ];
                },
                [
                    'days' => Util::Collection(function (Period $p) {
                        return new DayCollection($p->getDays());
                    }, null)
                ]
            ),
            'eventCategories' => Util::Collection(function (Camp $c) {
                return new EventCategoryCollection($c->getEventCategories());
            }, null)
        ];
    }

    /**
     * @param object $object
     * @return array
     * @throws \Exception
     */
    public function extract($object) {
        $auth = new AuthenticationService();


        /** @var Camp $camp */
        $camp = $object;
        return [
            'id' => $camp->getId(),
            'name' => $camp->getName(),
            'title' => $camp->getTitle(),
            'motto' => $camp->getMotto(),
            'role' => $camp->getRole($auth->getIdentity()),

//            'owner' => EntityLink::Create($camp->getOwner()),
            'creator' => EntityLink::Create($camp->getCreator()),
            'camp_type' => EntityLink::Create($camp->getCampType()),

            'camp_collaborations' => new EntityLinkCollection($camp->getCampCollaborations()),

            'periods' => new EntityLinkCollection($camp->getPeriods()),

            'event_categories' => new EntityLinkCollection($camp->getEventCategories()),
            'events' => Link::factory([
                'rel' => 'events',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.event',
                    'options' => ['query' => ['camp_id' => $camp->getId()]]
                ]
            ])
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
