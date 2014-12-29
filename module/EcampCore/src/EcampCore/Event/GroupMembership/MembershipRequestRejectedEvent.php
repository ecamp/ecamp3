<?php

namespace EcampCore\Event\GroupMembership;

use EcampCore\Entity\GroupMembership;
use EcampCore\Event\GroupMembershipEvent;

class MembershipRequestRejectedEvent extends GroupMembershipEvent  {

    const MembershipRequestRejected = 'membership-request-rejected';

    public function __construct($target, GroupMembership $groupMembership){
        parent::__construct(self::MembershipRequestRejected, $target, $groupMembership);
    }

}