<?php

namespace eCamp\Core\Hydrator;

use eCamp\Api\Collection\CampTypeCollection;
use eCamp\Api\Collection\GroupCollection;
use eCamp\Core\Entity\Organization;
use Zend\Hydrator\HydratorInterface;

class OrganizationHydrator implements HydratorInterface
{
    /**
     * @param object $object
     * @return array
     */
    public function extract($object)
    {
        /** @var Organization $organization */
        $organization = $object;
        return [
            'id' => $organization->getId(),
            'name' => $organization->getName(),

            'camp_types' => new CampTypeCollection($organization->getCampTypes())
        ];
    }

    /**
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        /** @var Organization $organization */
        $organization = $object;

        return $organization;
    }
}
