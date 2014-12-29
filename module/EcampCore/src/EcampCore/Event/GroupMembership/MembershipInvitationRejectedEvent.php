<?php

namespace EcampCore\Event\GroupMembership;

use EcampCore\Entity\GroupMembership;
use EcampCore\Event\GroupMembershipEvent;

class MembershipInvitationRejectedEvent extends GroupMembershipEvent  {

    const MembershipInvitationRejected = 'membership-invitation-rejected';

    public function __construct($target, GroupMembership $groupMembership){
        parent::__construct(self::MembershipInvitationRejected, $target, $groupMembership);
    }

}