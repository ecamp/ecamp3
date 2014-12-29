<?php
namespace EcampApi\Resource\Membership;

class MembershipBriefResource extends MembershipBaseResource
{
    protected function createObject(){
        return array(
            'id'            =>  ($this->membership != null ? $this->membership->getId() : null),
            'group'         =>  $this->group->getId(),
            'user'          =>  $this->user->getId(),
            'role'          =>  $this->getRole(),
            'status'        =>  $this->getStatus()
        );
    }
}
