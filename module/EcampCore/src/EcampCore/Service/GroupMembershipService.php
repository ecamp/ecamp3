<?php

namespace EcampCore\Service;

use EcampCore\Entity\User;
use EcampCore\Entity\Group;
use EcampCore\Entity\GroupMembership;

use EcampCore\Event\GroupMembership\MembershipGroupLeftEvent;
use EcampCore\Event\GroupMembership\MembershipInvitationAcceptedEvent;
use EcampCore\Event\GroupMembership\MembershipInvitationCreatedEvent;
use EcampCore\Event\GroupMembership\MembershipInvitationRejectedEvent;
use EcampCore\Event\GroupMembership\MembershipInvitationRevokedEvent;
use EcampCore\Event\GroupMembership\MembershipRequestAcceptedEvent;
use EcampCore\Event\GroupMembership\MembershipRequestCreatedEvent;
use EcampCore\Event\GroupMembership\MembershipRequestRejectedEvent;
use EcampCore\Event\GroupMembership\MembershipRequestRevokedEvent;
use EcampCore\Event\GroupMembership\MembershipUserKickedEvent;
use EcampLib\Service\ServiceBase;
use EcampCore\Acl\Privilege;
use EcampCore\Repository\GroupMembershipRepository;

class GroupMembershipService
    extends ServiceBase
{
    /** @var GroupMembershipRepository */
    private $groupMembershipRepo;

    public function __construct(
        GroupMembershipRepository $groupMembershipRepo
    ){
        $this->groupMembershipRepo = $groupMembershipRepo;
    }

    /**
     * @param User   $me
     * @param Group  $group
     * @param string $role
     */
    public function requestMembership(User $me, Group $group, $role = null)
    {
        $this->aclRequire($me, Privilege::USER_ADMINISTRATE);

        $groupMembership = $this->groupMembershipRepo->findByGroupAndUser($group, $me);

        $this->validationAssert(
            is_null($groupMembership),
            array("user" => "Membership can not be requested")
        );

        $groupMembership = GroupMembership::createRequest($me, $group, $role);
        $this->persist($groupMembership);

        $this->getEventManager()->trigger(new MembershipRequestCreatedEvent($this, $groupMembership));
    }

    /**
     * @param User  $me
     * @param Group $group
     */
    public function revokeRequest(User $me, Group $group)
    {
        $this->aclRequire($me, Privilege::USER_ADMINISTRATE);

        $groupMembership = $this->groupMembershipRepo->findByGroupAndUser($group, $me);

        $this->validationAssert(
            $groupMembership != null && $groupMembership->isRequest(),
            "There is no open Request"
        );

        $this->remove($groupMembership);

        $this->getEventManager()->trigger(new MembershipRequestRevokedEvent($this, $groupMembership));
    }

    /**
     * @param User   $manager
     * @param Group  $group
     * @param User   $user
     * @param string $role
     */
    public function acceptRequest(User $manager, Group $group, User $user, $role = null)
    {
        $this->aclRequire($manager, Privilege::USER_ADMINISTRATE);
        $this->aclRequire($group, Privilege::GROUP_ADMINISTRATE);

        $groupMembership = $this->groupMembershipRepo->findByGroupAndUser($group, $user);

        $this->validationAssert(
            $groupMembership != null && $groupMembership->isRequest(),
            "There is no open Request"
        );

        $groupMembership->acceptRequest($manager, $role);

        $this->getEventManager()->trigger(new MembershipRequestAcceptedEvent($this, $groupMembership));
    }

    /**
     * @param Group $group
     * @param User  $user
     */
    public function rejectRequest(Group $group, User $user)
    {
        $this->aclRequire($group, Privilege::GROUP_ADMINISTRATE);

        $groupMembership = $this->groupMembershipRepo->findByGroupAndUser($group, $user);

        $this->validationAssert(
            $groupMembership != null && $groupMembership->isRequest(),
            array('user' => 'There is no open Request')
        );

        $this->remove($groupMembership);

        $this->getEventManager()->trigger(new MembershipRequestRejectedEvent($this, $groupMembership));
    }

    /**
     * @param User   $manager
     * @param Group  $group
     * @param User   $user
     * @param string $role
     */
    public function inviteUser(User $manager, Group $group, User $user, $role = null)
    {
        $this->aclRequire($manager, Privilege::USER_ADMINISTRATE);
        $this->aclRequire($group, Privilege::GROUP_ADMINISTRATE);

        $groupMembership = $this->groupMembershipRepo->findByGroupAndUser($group, $user);
        echo $groupMembership;

        $this->validationAssert(
            is_null($groupMembership),
            array('user' => 'User can not be invited')
        );

        $groupMembership = GroupMembership::createInvitation($user, $group, $manager, $role);
        $this->persist($groupMembership);

        $this->getEventManager()->trigger(new MembershipInvitationCreatedEvent($this, $groupMembership));
    }

    /**
     * @param Group $group
     * @param User  $user
     */
    public function revokeInvitation(Group $group, User $user)
    {
        $this->aclRequire($group, Privilege::GROUP_ADMINISTRATE);

        $groupMembership = $this->groupMembershipRepo->findByGroupAndUser($group, $user);

        $this->validationAssert(
            $groupMembership != null && $groupMembership->isInvitation(),
            "There is no open Invitation"
           );

        $this->remove($groupMembership);

        $this->getEventManager()->trigger(new MembershipInvitationRevokedEvent($this, $groupMembership));
    }

    /**
     * @param User  $me
     * @param Group $group
     */
    public function acceptInvitation(User $me, Group $group)
    {
        $this->aclRequire($me, Privilege::USER_ADMINISTRATE);

        $groupMembership = $this->groupMembershipRepo->findByGroupAndUser($group, $me);

        $this->validationAssert(
            $groupMembership != null && $groupMembership->isInvitation(),
            "There is no open Invitation"
        );

        $groupMembership->acceptInvitation();

        $this->getEventManager()->trigger(new MembershipInvitationAcceptedEvent($this, $groupMembership));
    }

    /**
     * @param User  $me
     * @param Group $group
     */
    public function rejectInvitation(User $me, Group $group)
    {
        $this->aclRequire($me, Privilege::GROUP_ADMINISTRATE);

        $groupMembership = $this->groupMembershipRepo->findByGroupAndUser($group, $me);

        $this->validationAssert(
            $groupMembership != null && $groupMembership->isInvitation(),
            "There is no open Invitation"
        );

        $this->remove($groupMembership);

        $this->getEventManager()->trigger(new MembershipInvitationRejectedEvent($this, $groupMembership));
    }

    /**
     * @param User  $me
     * @param Group $group
     */
    public function leaveGroup(User $me, Group $group)
    {
        $this->aclRequire($me, Privilege::USER_ADMINISTRATE);

        $groupMembership = $this->groupMembershipRepo->findByGroupAndUser($group, $me);

        $this->validationAssert(
            $groupMembership != null && $groupMembership->isEstablished(),
            array('user' => 'User is not a Group Member')
        );

        $this->remove($groupMembership);

        $this->getEventManager()->trigger(new MembershipGroupLeftEvent($this, $groupMembership));
    }

    /**
     * @param Group $group
     * @param User  $user
     */
    public function kickUser(Group $group, User $user)
    {
        $this->aclRequire($group, Privilege::GROUP_ADMINISTRATE);

        $groupMembership = $this->groupMembershipRepo->findByGroupAndUser($group, $user);

        $this->validationAssert(
            $groupMembership != null && $groupMembership->isEstablished(),
            "User is not a Group Member"
        );

        $this->remove($groupMembership);

        $this->getEventManager()->trigger(new MembershipUserKickedEvent($this, $groupMembership));
    }

    public function changeRole(Group $group, User $user, $role)
    {
        $this->aclRequire($group, Privilege::GROUP_ADMINISTRATE);

        $groupMembership = $this->groupMembershipRepo->findByGroupAndUser($group, $user);

        $groupMembership->setRole($role);
    }

}
