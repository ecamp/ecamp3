<?php

namespace eCamp\Core\Hydrator;

use eCamp\Api\Collection\GroupCollection;
use eCamp\Core\Entity\Group;
use Zend\Hydrator\HydratorInterface;

class GroupHydrator implements HydratorInterface
{
    /**
     * @param object $object
     * @return array
     */
    public function extract($object)
    {
        /** @var Group $group */
        $group = $object;
        return [
            'id' => $group->getId(),
            'name' => $group->getName(),
            'description' => $group->getDescription(),
            'display_name' => $group->getDisplayName(),
            'organization' => $group->getOrganization(),

            'parent' => $group->getParent(),
            'children' => new GroupCollection($group->getChildren()),
        ];
    }

    /**
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        /** @var Group $group */
        $group = $object;

        $group->setName($data['name']);
        $group->setDescription($data['description']);

        return $group;
    }
}
