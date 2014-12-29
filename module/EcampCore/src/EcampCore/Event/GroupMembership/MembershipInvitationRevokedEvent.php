<?php

namespace EcampCore\Event\GroupMembership;

use EcampCore\Entity\GroupMembership;
use EcampCore\Event\GroupMembershipEvent;

class MembershipInvitationRevokedEvent extends GroupMembershipEvent  {

    const MembershipInvitationRevoked = 'membership-invitation-revoked';

    public function __construct($target, GroupMembership $groupMembership){
        parent::__construct(self::MembershipInvitationRevoked, $target, $groupMembership);
    }

}