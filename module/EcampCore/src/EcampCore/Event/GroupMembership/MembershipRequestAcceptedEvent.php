<?php

namespace EcampCore\Event\GroupMembership;

use EcampCore\Entity\GroupMembership;
use EcampCore\Event\GroupMembershipEvent;

class MembershipRequestAcceptedEvent extends GroupMembershipEvent  {

    const MembershipRequestAccepted = 'membership-request-accepted';

    public function __construct($target, GroupMembership $groupMembership){
        parent::__construct(self::MembershipRequestAccepted, $target, $groupMembership);
    }

}