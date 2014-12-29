<?php

namespace EcampCore\Event;

use EcampCore\Entity\GroupMembership;
use Zend\EventManager\Event;

class GroupMembershipEvent extends Event {

    /**
     * @var GroupMembership
     */
    protected $groupMembership;

    public function __construct($name, $target, GroupMembership $groupMembership){
        parent::__construct($name, $target);

        $this->groupMembership = $groupMembership;
    }

    /**
     * @return GroupMembership
     */
    public function getGroupMembership(){
        return $this->groupMembership;
    }

}