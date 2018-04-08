<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\GroupMembership;
use Zend\Hydrator\HydratorInterface;

class GroupMembershipHydrator implements HydratorInterface
{
    /**
     * @param object $object
     * @return array
     */
    public function extract($object)
    {
        /** @var GroupMembership $groupMembership */
        $groupMembership = $object;
        return [
            'id' => $groupMembership->getId(),
            'group' => $groupMembership->getGroup(),
            'user' => $groupMembership->getUser(),
            'role' => $groupMembership->getRole(),
            'status' => $groupMembership->getStatus(),
        ];
    }

    /**
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        /** @var GroupMembership $groupMembership */
        $groupMembership = $object;

        return $groupMembership;
    }
}
