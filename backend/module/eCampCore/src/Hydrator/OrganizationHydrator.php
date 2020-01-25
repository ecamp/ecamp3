<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Organization;
use eCamp\Lib\Entity\EntityLinkCollection;
use eCampApi\V1\Rest\CampType\CampTypeCollection;
use Zend\Hydrator\HydratorInterface;

class OrganizationHydrator implements HydratorInterface {
    public static function HydrateInfo() {
        return [
        ];
    }

    /**
     * @param object $object
     * @return array
     */
    public function extract($object) {
        /** @var Organization $organization */
        $organization = $object;
        return [
            'id' => $organization->getId(),
            'name' => $organization->getName(),

            'camp_types' => new EntityLinkCollection($organization->getCampTypes())
        ];
    }

    /**
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var Organization $organization */
        $organization = $object;

        $organization->setName($data['name']);

        return $organization;
    }
}
