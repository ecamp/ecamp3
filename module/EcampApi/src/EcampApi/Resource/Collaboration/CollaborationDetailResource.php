<?php
namespace EcampApi\Resource\Collaboration;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;
use EcampCore\Entity\CampCollaboration as Collaboration;
use EcampApi\Resource\User\UserBriefResource;
use EcampApi\Resource\Camp\CampBriefResource;

class CollaborationDetailResource extends CollaborationBaseResource
{
    protected function createObject()
    {
        return array(
            'id'        =>  ($this->collaboration != null ? $this->collaboration->getId() : null),
            'user'      =>  new UserBriefResource($this->user),
            'camp'      =>  new CampBriefResource($this->camp),
            'role'      =>  $this->getRole(),
            'status'    =>  $this->getStatus(),
        );
    }
}
