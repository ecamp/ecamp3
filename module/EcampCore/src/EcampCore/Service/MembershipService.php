<?php
/*
 * Copyright (C) 2011 Pirmin Mattmann
 *
 * This file is part of eCamp.
 *
 * eCamp is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * eCamp is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with eCamp.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace EcampCore\Service;

use EcampCore\Entity\User;
use EcampCore\Entity\Group;
use EcampCore\Entity\UserGroup;
use EcampLib\Service\ServiceBase;

/**
 * @method EcampCore\Service\MembershipService Simulate
 */
class MembershipService extends ServiceBase
{

    /**
     * Returns the requested UserGroup Entity.
     * - For a id, the UserGroup-Entity
     * - For a Group the UserGroup-Entity for me in this Group
     * - For a Group and User the corresponding UserGroup-Entity
     *
     * @param  Group|integer $GroupOrId
     * @return UserGroup
     */
    public function Get($GroupOrId, $user = null)
    {
        if ($GroupOrId instanceof Group) {
            $user = $user ?: $this->getContextProvider()->getMe();

            return $this->repo()->userGroupRepository()->findOneBy(
                array('group' => $GroupOrId, 'user' => $user)
            );
        }

        return $this->repo()->userGroupRepository()->find($GroupOrId);
    }

    /**
     * Returns a list of UserGroup for each Member in a Group
     *
     * @param  Group       $group
     * @return UserGroup[]
     */
    public function GetMembers(Group $group)
    {
        return $this->repo()->userGroupRepository()->findMembershipsOfGroup($group);
    }

    /**
     * Returns a list of UserGroup for each Group the User is a Member of.
     *
     * @param  User        $user
     * @return UserGroup[]
     */
    public function GetGroups(User $user)
    {
        return $this->repo()->userGroupRepository()->findMembershipsOfUser($user);
    }

    /**
     * Returns a List of UserGroup for each open MembershipRequest for the
     * given Group or the Group in Context (if no group is defined)
     *
     * @param  Group       $group
     * @return UserGroup[]
     */
    public function GetRequests()
    {
        $group = $this->getContextProvider()->getGroup();

        return $this->repo()->userGroupRepository()->findMembershipRequests($group);
    }

    /**
     * Returns a List of UserGroup for each open Invitation for the
     * given User or the authenticated User (if no user is defined)
     *
     * @param  User        $user
     * @return UserGroup[]
     */
    public function GetInvitations()
    {
        $user = $this->getContextProvider()->getMe();

        return $this->repo()->userGroupRepository()->findMembershipInvitations($user);
    }

    /**
     * The authenticated User requests for Membership in the given Group
     *
     * @param Group $group
     * @param $role
     */
    public function RequestMembership(Group $group, $role = UserGroup::ROLE_MEMBER)
    {
        $userGroup = $this->get($group);

        if ($userGroup != null) {
            $this->addValidationMessage("Membership or Membership Request exists allready");
        } else {
            $userGroup = new UserGroup();
            $userGroup->setGroup($group);
            $userGroup->setUser($this->getContextProvider()->getMe());
            $userGroup->setRequestedRole($role);
            $userGroup->acceptInvitation();

            $this->getEM()->persist($userGroup);
        }
    }

    /**
     * Deletes the MembershipRequest to the given Group
     *
     * @param Group $group
     */
    public function DeleteRequest(Group $group)
    {
        $userGroup = $this->Get($group);

        if ($userGroup == null || !$userGroup->isOpenRequest()) {
            $this->addValidationMessage("There is no Request to delete");
        } else {
            $this->getEM()->remove($userGroup);
        }

    }

    /**
     * Accepts the MembershipRequest of the given User to the current Group
     *
     * @param User $user
     */
    public function AcceptRequest(User $user)
    {
        $group = $this->getContextProvider()->getGroup();
        $userGroup = $this->Get($group, $user);

        if ($userGroup == null || !$userGroup->isOpenRequest()) {
            $this->addValidationMessage("There is no Request to accept");
        } else {
            $userGroup->acceptRequest($this->getContextProvider()->getMe());
        }
    }

    /**
     * Rejects the MembershipRequest of the given User to the current Group
     *
     * @param User $user
     */
    public function RejectRequest(User $user)
    {
        $group = $this->getContextProvider()->getGroup();
        $userGroup = $this->Get($group, $user);

        if ($userGroup == null || !$userGroup->isOpenRequest()) {
            $this->addValidationMessage("There is no Request to reject");
        } else {
            $this->getEM()->remove($userGroup);
        }
    }

    /**
     * The authenticated user leaves the current group
     *
     * @param Group $group
     */
    public function LeaveGroup()
    {
        $group = $this->getContextProvider()->getGroup();
        $userGroup = $this->Get($group);

        if ($userGroup == null || $userGroup->isOpen()) {
            $this->addValidationMessage("You are not a Member of this group. You can not leave this Group");
        } else {
            $this->getEM()->remove($userGroup);
        }
    }

    /**
     * The autheticated user invites the given user to the current Group
     *
     * @param User $user
     * @param $role
     */
    public function InviteUser(User $user, $role = UserGroup::ROLE_MEMBER)
    {
        $group = $this->getContextProvider()->getGroup();
        $userGroup = $this->get($group, $user);

        if ($userGroup != null) {
            $this->addValidationMessage("Membership or Membership Invitation exists allready");
        } else {
            $userGroup = new UserGroup();
            $userGroup->setGroup($group);
            $userGroup->setUser($user);
            $userGroup->setRequestedRole($role);
            $userGroup->acceptRequest($this->getContextProvider()->getMe());

            $this->getEM()->persist($userGroup);
        }
    }

    /**
     * Deletes the invitation of the given user to the current Group
     *
     * @param User $user
     */
    public function DeleteInvitation(User $user)
    {
        $group = $this->getContextProvider()->getGroup();
        $userGroup = $this->Get($group, $user);

        if ($userGroup == null || !$userGroup->isOpenInvitation()) {
            $this->addValidationMessage("There is no Invitation to delete");
        } else {
            $this->getEM()->remove($userGroup);
        }
    }

    /**
     * The authenticated user accepts the invitation to the given group
     *
     * @param User $user
     */
    public function AcceptInvitation(Group $group)
    {
        $userGroup = $this->Get($group);

        if ($userGroup == null || !$userGroup->isOpenInvitation()) {
            $this->addValidationMessage("There is no Invitation to accept");
        } else {
            $userGroup->acceptInvitation();
        }
    }

    /**
     * The authenticated user rejects the invitation to the given group
     *
     * @param User $user
     */
    public function RejectInvitation(Group $group)
    {
        $userGroup = $this->Get($group);

        if ($userGroup == null || !$userGroup->isOpenInvitation()) {
            $this->addValidationMessage("There is no Invitation to accept");
        } else {
            $this->getEM()->remove($userGroup);
        }
    }

    /**
     * The authenticated user kicks the given user out of the current group
     *
     * @param User $user
     */
    public function KickOutUser(User $user)
    {
        $group = $this->getContextProvider()->getGroup();
        $userGroup = $this->Get($group, $user);

        if ($userGroup == null || $userGroup->isOpen()) {
            $this->addValidationMessage("This user is not a Member of this Group. You can not kick him out!");
        } else {
            $this->getEM()->remove($userGroup);
        }
    }

}
