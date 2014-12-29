<?php

namespace EcampCore\Event\GroupMembership;

use EcampCore\Entity\GroupMembership;
use EcampCore\Event\GroupMembershipEvent;

class MembershipUserKickedEvent extends GroupMembershipEvent  {

    const MembershipUserKicked = 'membership-user-kicked';

    public function __construct($target, GroupMembership $groupMembership){
        parent::__construct(self::MembershipUserKicked, $target, $groupMembership);
    }

}