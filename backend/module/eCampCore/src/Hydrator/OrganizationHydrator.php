<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Organization;
use Laminas\Hydrator\HydratorInterface;

class OrganizationHydrator implements HydratorInterface {
    public static function HydrateInfo(): array {
        return [];
    }

    /**
     * @param object $object
     */
    public function extract($object): array {
        /** @var Organization $organization */
        $organization = $object;

        return [
            'id' => $organization->getId(),
            'name' => $organization->getName(),
        ];
    }

    /**
     * @param object $object
     */
    public function hydrate(array $data, $object): Organization {
        /** @var Organization $organization */
        $organization = $object;

        if (isset($data['name'])) {
            $organization->setName($data['name']);
        }

        return $organization;
    }
}
