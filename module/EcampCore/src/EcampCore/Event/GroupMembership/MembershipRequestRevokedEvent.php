<?php

namespace EcampCore\Event\GroupMembership;

use EcampCore\Entity\GroupMembership;
use EcampCore\Event\GroupMembershipEvent;

class MembershipRequestRevokedEvent extends GroupMembershipEvent  {

    const MembershipRequestRevoked = 'membership-request-revoked';

    public function __construct($target, GroupMembership $groupMembership){
        parent::__construct(self::MembershipRequestRevoked, $target, $groupMembership);
    }

}