<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\CampCollaboration;
use eCamp\Lib\Entity\EntityLink;
use eCamp\Lib\Hydrator\Util;
use Laminas\Hydrator\HydratorInterface;

class CampCollaborationHydrator implements HydratorInterface {
    public static function HydrateInfo() {
        return [
            'camp' => Util::Entity(function (CampCollaboration $cc) {
                return $cc->getCamp();
            }),
            'user' => Util::Entity(function (CampCollaboration $cc) {
                return $cc->getUser();
            }),
        ];
    }

    /**
     * @param object $object
     *
     * @return array
     */
    public function extract($object) {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = $object;

        return [
            'id' => $campCollaboration->getId(),
            'role' => $campCollaboration->getRole(),
            'status' => $campCollaboration->getStatus(),
            'inviteEmail' => $campCollaboration->getInviteEmail(),

            'camp' => EntityLink::Create($campCollaboration->getCamp()),
            'user' => EntityLink::Create($campCollaboration->getUser()),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        // @var CampCollaboration $campCollaboration
        return $object;
    }
}
