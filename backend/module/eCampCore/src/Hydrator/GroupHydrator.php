<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Group;
use eCamp\Lib\Entity\EntityLink;
use eCamp\Lib\Entity\EntityLinkCollection;
use Laminas\Hydrator\HydratorInterface;

class GroupHydrator implements HydratorInterface {
    public static function HydrateInfo(): array {
        return [
        ];
    }

    /**
     * @param object $object
     */
    public function extract($object): array {
        /** @var Group $group */
        $group = $object;

        return [
            'id' => $group->getId(),
            'name' => $group->getName(),
            'description' => $group->getDescription(),
            'displayName' => $group->getDisplayName(),

            'organization' => EntityLink::Create($group->getOrganization()),
            'parent' => EntityLink::Create($group->getParent()),
            'children' => new EntityLinkCollection($group->getChildren()),
        ];
    }

    /**
     * @param object $object
     */
    public function hydrate(array $data, $object): Group {
        /** @var Group $group */
        $group = $object;

        if (isset($data['name'])) {
            $group->setName($data['name']);
        }
        if (isset($data['description'])) {
            $group->setDescription($data['description']);
        }

        return $group;
    }
}
