<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\CampType;
use eCampApi\V1\Rest\EventType\EventTypeCollection;
use Zend\Hydrator\HydratorInterface;
use ZF\Hal\Link\Link;

class CampTypeHydrator implements HydratorInterface {
    /**
     * @param object $object
     * @return array
     */
    public function extract($object) {
        /** @var CampType $campType */
        $campType = $object;

        return [
            'id' => $campType->getId(),
            'name' => $campType->getName(),
            'is_js' => $campType->getIsJS(),
            'is_course' => $campType->getIsCourse(),

//            'organization' => new EntityLink($campType->getOrganization()),
            'organization' => Link::factory([
                'rel' => 'organization',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.organization',
                    'params' => [ 'organization_id' => $campType->getOrganization()->getId() ]
                ]
            ]),

            'event_types' => new EventTypeCollection($campType->getEventTypes()),

            'event_types_link' => Link::factory([
                'rel' => 'event_types_link',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.event-type',
                    'options' => ['query' => ['camp_type_id' => $campType->getId()]]
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
        /** @var CampType $campType */
        $campType = $object;

        $campType->setName($data['name']);
        $campType->setIsJS($data['is_js']);
        $campType->setIsCourse($data['is_course']);
        $campType->setOrganization($data['organization']);

        return $campType;
    }
}
