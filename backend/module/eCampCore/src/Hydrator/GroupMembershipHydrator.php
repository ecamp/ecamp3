<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\GroupMembership;
use Zend\Hydrator\HydratorInterface;

class GroupMembershipHydrator implements HydratorInterface {

    public static function HydrateInfo() {
        return [
        ];
    }

    /**
     * @param object $object
     * @return array
     */
    public function extract($object) {
        /** @var GroupMembership $groupMembership */
        $groupMembership = $object;
        return [
            'id' => $groupMembership->getId(),
//            'group' => $groupMembership->getGroup(),
//            'user' => $groupMembership->getUser(),
            'role' => $groupMembership->getRole(),
            'status' => $groupMembership->getStatus(),
        ];
    }

    /**
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var GroupMembership $groupMembership */
        $groupMembership = $object;

        $groupMembership->setGroup($data['group']);
        $groupMembership->setUser($data['user']);
        $groupMembership->setRole($data['role']);
        $groupMembership->setStatus($data['status']);

        return $groupMembership;
    }
}
