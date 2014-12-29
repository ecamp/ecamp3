<?php

namespace EcampCore\Event\GroupMembership;

use EcampCore\Entity\GroupMembership;
use EcampCore\Event\GroupMembershipEvent;

class MembershipInvitationCreatedEvent extends GroupMembershipEvent  {

    const MembershipInvitationCreated = 'membership-invitation-created';

    public function __construct($target, GroupMembership $groupMembership){
        parent::__construct(self::MembershipInvitationCreated, $target, $groupMembership);
    }

}