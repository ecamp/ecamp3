<?php

namespace eCamp\Core\Hydrator;

use eCamp\Api\Collection\EventTypeCollection;
use eCamp\Core\Entity\CampType;
use Zend\Hydrator\HydratorInterface;

class CampTypeHydrator implements HydratorInterface
{
    /**
     * @param object $object
     * @return array
     */
    public function extract($object)
    {
        /** @var CampType $campType */
        $campType = $object;
        return [
            'id' => $campType->getId(),
            'name' => $campType->getName(),
            'is_js' => $campType->getIsJS(),
            'is_course' => $campType->getIsCourse(),
            'organization' => $campType->getOrganization(),
            'event_types' => new EventTypeCollection($campType->getEventTypes()),
        ];
    }

    /**
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        /** @var CampType $campType */
        $campType = $object;

        $campType->setName($data['name']);
        $campType->setIsJS($data['is_js']);
        $campType->setIsCourse($data['is_course']);
        $campType->setOrganization($data['organization']);

        return $campType;
    }
}
