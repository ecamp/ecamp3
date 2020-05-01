<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Group;
use eCamp\Lib\Entity\EntityLink;
use eCamp\Lib\Entity\EntityLinkCollection;
use Zend\Hydrator\HydratorInterface;

class GroupHydrator implements HydratorInterface {
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
        /** @var Group $group */
        $group = $object;

        return [
            'id' => $group->getId(),
            'name' => $group->getName(),
            'description' => $group->getDescription(),
            'display_name' => $group->getDisplayName(),

            'organization' => EntityLink::Create($group->getOrganization()),
            'parent' => EntityLink::Create($group->getParent()),
            'children' => new EntityLinkCollection($group->getChildren()),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var Group $group */
        $group = $object;

        if( isset($data['name']) ) $group->setName($data['name']);
        if( isset($data['description']) ) $group->setDescription($data['description']);

        return $group;
    }
}
