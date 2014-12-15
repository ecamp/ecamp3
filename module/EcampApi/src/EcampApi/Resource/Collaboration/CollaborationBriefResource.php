<?php
namespace EcampApi\Resource\Collaboration;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;
use EcampCore\Entity\CampCollaboration as Collaboration;

class CollaborationBriefResource extends CollaborationBaseResource
{
    protected function createObject()
    {
        return array(
            'id'        =>  ($this->collaboration != null ? $this->collaboration->getId() : null),
            'user'      =>  $this->user->getId(),
            'camp'      =>  $this->camp->getId(),
            'role'      =>  $this->getRole(),
            'status'    =>  $this->getStatus(),
        );
    }
}
