<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\GroupMembership;
use Laminas\Hydrator\HydratorInterface;

class GroupMembershipHydrator implements HydratorInterface {
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
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var GroupMembership $groupMembership */
        $groupMembership = $object;

        if (isset($data['group'])) {
            $groupMembership->setGroup($data['group']);
        }
        if (isset($data['user'])) {
            $groupMembership->setUser($data['user']);
        }
        if (isset($data['role'])) {
            $groupMembership->setRole($data['role']);
        }
        if (isset($data['status'])) {
            $groupMembership->setStatus($data['status']);
        }

        return $groupMembership;
    }
}
