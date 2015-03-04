<?php

namespace EcampCore\Event;

use EcampCore\Entity\GroupMembership;
use Zend\EventManager\Event;

class GroupMembershipEvent extends Event {

    const MembershipInvitationCreated = 'membership-invitation-created';
    const MembershipInvitationRevoked = 'membership-invitation-revoked';
    const MembershipInvitationAccepted = 'membership-invitation-accepted';
    const MembershipInvitationRejected = 'membership-invitation-rejected';

    const MembershipRequestCreated = 'membership-request-created';
    const MembershipRequestRevoked = 'membership-request-revoked';
    const MembershipRequestAccepted = 'membership-request-accepted';
    const MembershipRequestRejected = 'membership-request-rejected';

    const MembershipUserKicked = 'membership-user-kicked';
    const MembershipGroupLeft = 'membership-group-left';


    public static function InvitationCreated($target, GroupMembership $groupMembership){
        return new self(self::MembershipInvitationCreated, $target, $groupMembership);
    }
    public static function InvitationRevoked($target, GroupMembership $groupMembership){
        return new self(self::MembershipInvitationRevoked, $target, $groupMembership);
    }
    public static function InvitationAccepted($target, GroupMembership $groupMembership){
        return new self(self::MembershipInvitationAccepted, $target, $groupMembership);
    }
    public static function InvitationRejected($target, GroupMembership $groupMembership){
        return new self(self::MembershipInvitationRejected, $target, $groupMembership);
    }

    public static function RequestCreated($target, GroupMembership $groupMembership){
        return new self(self::MembershipInvitationCreated, $target, $groupMembership);
    }
    public static function RequestRevoked($target, GroupMembership $groupMembership){
        return new self(self::MembershipInvitationRevoked, $target, $groupMembership);
    }
    public static function RequestAccepted($target, GroupMembership $groupMembership){
        return new self(self::MembershipInvitationAccepted, $target, $groupMembership);
    }
    public static function RequestRejected($target, GroupMembership $groupMembership){
        return new self(self::MembershipInvitationRejected, $target, $groupMembership);
    }

    public static function UserKicked($target, GroupMembership $groupMembership){
        return new self(self::MembershipUserKicked, $target, $groupMembership);
    }
    public static function GroupLeft($target, GroupMembership $groupMembership){
        return new self(self::MembershipGroupLeft, $target, $groupMembership);
    }

    /**
     * @var GroupMembership
     */
    protected $groupMembership;

    public function __construct($name, $target, GroupMembership $groupMembership){
        parent::__construct($name, $target);

        $this->groupMembership = $groupMembership;
    }

    /**
     * @return GroupMembership
     */
    public function getGroupMembership(){
        return $this->groupMembership;
    }

}