<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\CampCollaboration;
use eCamp\Lib\Entity\EntityLink;
use Zend\Hydrator\HydratorInterface;

class CampCollaborationHydrator implements HydratorInterface {

    public static function HydrateInfo() {
        return [
        ];
    }

    /**
     * @param object $object
     * @return array
     */
    public function extract($object) {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = $object;
        return [
            'id' => $campCollaboration->getId(),
            'role' => $campCollaboration->getRole(),
            'status' => $campCollaboration->getStatus(),

            'camp' => EntityLink::Create($campCollaboration->getCamp()),
            'user' => EntityLink::Create($campCollaboration->getUser()),
        ];
    }

    /**
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = $object;



        return $campCollaboration;
    }
}
