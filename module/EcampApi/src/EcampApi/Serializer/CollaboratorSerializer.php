<?php

namespace EcampApi\Serializer;

use EcampCore\Entity\User;
use EcampCore\Entity\Camp;
use EcampCore\Entity\CampCollaboration;

class CollaboratorSerializer extends BaseSerializer
{
    public function serialize($cc)
    {
        /* @var $cc CampCollaboration */
        $userSerializer = new UserSerializer($this->format, $this->router);
        $campSerializer = new CampSerializer($this->format, $this->router);

        return array(
            'id'		=> 	$cc->getId(),
            'href'		=>	$this->getCollaboratorHref($cc),
            'user'		=>	$userSerializer->getReference($cc->getUser()),
            'camp'		=>	$campSerializer->getReference($cc->getCamp()),
            'role'		=>	$cc->getRole(),
            'status'	=>  $cc->getStatus()
        );
    }

    public function getCollectionReference($collectionOwner)
    {
        if ($collectionOwner instanceof Camp) {
            return array('href' => $this->getCamp_CollectionReference($collectionOwner));
        }

        return null;
    }

    private function getCamp_CollectionReference(Camp $camp)
    {
        return
            $this->router->assemble(
                array(
                    'controller' => 'collaborators',
                    'action' => 'get',
                    'format' => $this->format,
                    'camp' => $camp->getId()
                ),
                array(
                    'name' => 'api/camp/rest'
                )
            );
    }

    private function getCollaboratorHref(CampCollaboration $campCollaboration)
    {
        return
            $this->router->assemble(
                array(
                    'controller' => 'collaborators',
                    'action' => 'get',
                    'id' => $campCollaboration->getId(),
                    'format' => $this->format
                ),
                array(
                    'name' => 'api/rest'
                )
            );
    }
}
