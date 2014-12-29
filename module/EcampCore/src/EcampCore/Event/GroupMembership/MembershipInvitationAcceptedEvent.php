<?php

namespace EcampCore\Event\GroupMembership;

use EcampCore\Entity\GroupMembership;
use EcampCore\Event\GroupMembershipEvent;

class MembershipInvitationAcceptedEvent extends GroupMembershipEvent  {

    const MembershipInvitationAccepted = 'membership-invitation-accepted';

    public function __construct($target, GroupMembership $groupMembership){
        parent::__construct(self::MembershipInvitationAccepted, $target, $groupMembership);
    }

}