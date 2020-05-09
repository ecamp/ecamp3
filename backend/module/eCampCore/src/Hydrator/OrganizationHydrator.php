<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Organization;
use eCamp\Lib\Entity\EntityLinkCollection;
use Zend\Hydrator\HydratorInterface;

class OrganizationHydrator implements HydratorInterface {
    public static function HydrateInfo() {
        return [
        ];
    }

    /**
     * @param object $object
     *
     * @return array
     */
    public function extract($object) {
        /** @var Organization $organization */
        $organization = $object;

        return [
            'id' => $organization->getId(),
            'name' => $organization->getName(),

            'campTypes' => new EntityLinkCollection($organization->getCampTypes()),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var Organization $organization */
        $organization = $object;

        if (isset($data['name'])) {
            $organization->setName($data['name']);
        }

        return $organization;
    }
}
