<?php

namespace EcampCore\Event\GroupMembership;

use EcampCore\Entity\GroupMembership;
use EcampCore\Event\GroupMembershipEvent;

class MembershipGroupLeftEvent extends GroupMembershipEvent  {

    const MembershipGroupLeft = 'membership-group-left';

    public function __construct($target, GroupMembership $groupMembership){
        parent::__construct(self::MembershipGroupLeft, $target, $groupMembership);
    }

}