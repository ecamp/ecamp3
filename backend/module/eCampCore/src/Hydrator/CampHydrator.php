<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Period;
use eCamp\Lib\Entity\EntityLink;
use eCamp\Lib\Entity\EntityLinkCollection;
use eCamp\Lib\Hydrator\Util;
use eCampApi\V1\Rest\Category\CategoryCollection;
use eCampApi\V1\Rest\Day\DayCollection;
use eCampApi\V1\Rest\Period\PeriodCollection;
use Laminas\ApiTools\Hal\Link\Link;
use Laminas\Authentication\AuthenticationService;
use Laminas\Hydrator\HydratorInterface;

class CampHydrator implements HydratorInterface {
    public static function HydrateInfo(): array {
        return [
            'periods' => Util::Collection(
                function (Camp $c) {
                    return new PeriodCollection($c->getPeriods());
                },
                null,
                // TODO: kann das weg? war nur Democode, oder?
                /*
                function (Camp $c) {
                    return [
                        Link::factory([
                            'rel' => 'campCollaborations',
                            'route' => [
                                'name' => 'e-camp-api.rest.doctrine.camp-collaboration',
                                'options' => ['query' => ['campId' => $c->getId()]],
                            ],
                        ]),
                    ];
                },*/
                [
                    'days' => Util::Collection(function (Period $p) {
                        return new DayCollection($p->getDays());
                    }, null),
                ]
            ),
            'activityCategories' => Util::Collection(function (Camp $c) {
                return new CategoryCollection($c->getCategories());
            }, null),
        ];
    }

    /**
     * @param object $object
     *
     * @throws \Exception
     */
    public function extract($object): array {
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

            'campCollaborations' => new EntityLinkCollection($camp->getCampCollaborations()),

            'periods' => new EntityLinkCollection($camp->getPeriods()),

            'categories' => new EntityLinkCollection($camp->getCategories()),
            'activities' => Link::factory([
                'rel' => 'activities',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.activity',
                    'options' => ['query' => ['campId' => $camp->getId()]],
                ],
            ]),
            'materialLists' => Link::factory([
                'rel' => 'materialLists',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.material-list',
                    'options' => ['query' => ['campId' => $camp->getId()]],
                ],
            ]),
        ];
    }

    /**
     * @param object $object
     */
    public function hydrate(array $data, $object): Camp {
        /** @var Camp $camp */
        $camp = $object;

        if (isset($data['title'])) {
            $camp->setTitle($data['title']);
        }
        if (isset($data['motto'])) {
            $camp->setMotto($data['motto']);
        }

        return $camp;
    }
}
