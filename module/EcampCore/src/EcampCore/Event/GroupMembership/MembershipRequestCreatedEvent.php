<?php

namespace EcampCore\Event\GroupMembership;

use EcampCore\Entity\GroupMembership;
use EcampCore\Event\GroupMembershipEvent;

class MembershipRequestCreatedEvent extends GroupMembershipEvent  {

    const MembershipRequestCreated = 'membership-request-created';

    public function __construct($target, GroupMembership $groupMembership){
        parent::__construct(self::MembershipRequestCreated, $target, $groupMembership);
    }

}