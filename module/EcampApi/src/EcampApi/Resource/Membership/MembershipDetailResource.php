<?php
namespace EcampApi\Resource\Membership;

use EcampApi\Resource\User\UserBriefResource;
use EcampApi\Resource\Group\GroupBriefResource;

class MembershipDetailResource extends MembershipBaseResource
{
    protected function createObject()
    {
        return array(
            'id'        =>  ($this->membership != null ? $this->membership->getId() : null),
            'user'      =>  new UserBriefResource($this->user),
            'group'     =>  new GroupBriefResource($this->group),
            'role'      =>  $this->getRole(),
            'status'    =>  $this->getStatus(),
        );
    }
}
