<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\CampCollaboration;
use Zend\Hydrator\HydratorInterface;

class CampCollaborationHydrator implements HydratorInterface
{
    /**
     * @param object $object
     * @return array
     */
    public function extract($object)
    {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = $object;
        return [
            'id' => $campCollaboration->getId(),
            'camp' => $campCollaboration->getCamp(),
            'user' => $campCollaboration->getUser(),
            'role' => $campCollaboration->getRole(),
            'status' => $campCollaboration->getStatus(),
        ];
    }

    /**
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = $object;



        return $campCollaboration;
    }
}
