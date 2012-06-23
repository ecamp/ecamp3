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

namespace CoreApi\Service;

use Core\Service\ServiceBase;

use CoreApi\Entity\User;
use CoreApi\Entity\Group;
use CoreApi\Entity\UserGroup;


class MembershipService extends ServiceBase
{
	
	
	/**
	 * @var Core\Repository\UserGroupRepository
	 * @Inject Core\Repository\UserGroupRepository
	 */
	protected $userGroupRepo;
	
	public function _setupAcl()
	{
		
	}
	
	/**
	 * Returns the requested UserGroup Entity.
	 * - For a id, the UserGroup-Entity
	 * - For a Group the UserGroup-Entity for me in this Group
	 * - For a Group and User the corresponding UserGroup-Entity
	 * 
	 * @param Group|integer $GroupOrId
	 * @return UserGroup
	 */
	public function Get($GroupOrId, $user = null)
	{
		if($GroupOrId instanceof Group)
		{
			$user = $user ?: $this->getContext()->getMe();
			
			return $this->userGroupRepo->findBy(
				array('group' => $GroupOrId, 'user' => $user)
			);
		}
		
		return $this->userGroupRepo->find($GroupOrId);
	}
	
	/**
	 * Returns a list of UserGroup for each Member in a Group
	 * 
	 * @param Group $group
	 * @return UserGroup[]
	 */
	public function GetMembers(Group $group)
	{
		return $this->userGroupRepo->findMembershipsOfGroup($group);
	}
	
	/**
	 * Returns a list of UserGroup for each Group the User is a Member of.
	 * 
	 * @param User $user
	 * @return UserGroup[]
	 */
	public function GetGroups(User $user)
	{
		return $this->userGroupRepo->findMembershipsOfUser($user);
	}
	
	/**
	 * Returns a List of UserGroup for each open MembershipRequest for the 
	 * given Group or the Group in Context (if no group is defined)
	 * 
	 * @param Group $group
	 * @return UserGroup[]
	 */
	public function GetRequests(Group $group = null)
	{
		$group = $group ?: $this->getContext()->getGroup();
		
		return $this->userGroupRepo->findMembershipRequests($group);
	}
	
	/**
	 * Returns a List of UserGroup for each open Invitation for the 
	 * given User or the authenticated User (if no user is defined)
	 * 
	 * @param User $user
	 * @return UserGroup[]
	 */
	public function GetInvitations(User $user = null)
	{
		$user = $user ?: $this->getContext()->getMe();
		
		return $this->userGroupRepo->findMembershipInvitations($user);
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
		
		if($userGroup != null){
			$this->addValidationMessage("Membership or Membership Request exists allready");
		}
		
		$userGroup = new UserGroup();
		$userGroup->setGroup($group);
		$userGroup->setUser($this->getContext()->getMe());
		$userGroup->setRequestedRole($role);
		$userGroup->acceptInvitation();
		
		$this->em->persist($userGroup);
	}
	
	/**
	 * Deletes the MembershipRequest to the given Group
	 * 
	 * @param Group $group
	 */
	public function DeleteRequest(Group $group)
	{
		$userGroup = $this->Get($group);
		
		if($userGroup == null || !$userGroup->isOpenRequest()){
			$this->addValidationMessage("There is no Request to delete");
		}
		
		$this->em->remove($userGroup);
	}
	
	/**
	 * Accepts the MembershipRequest of the given User to the current Group
	 * 
	 * @param User $user
	 */
	public function AcceptRequest(User $user)
	{
		$group = $this->getContext()->getGroup();
		$userGroup = $this->Get($group, $user);
		
		if($userGroup == null || !$userGroup->isOpenRequest()){
			$this->addValidationMessage("There is no Request to accept");
		}
		
		$userGroup->acceptRequest($this->getContext()->getMe());
	}
	
	/**
	 * Rejects the MembershipRequest of the given User to the current Group
	 * 
	 * @param User $user
	 */
	public function RejectRequest(User $user)
	{
		$group = $this->getContext()->getGroup();
		$userGroup = $this->Get($group, $user);
		
		if($userGroup == null || !$userGroup->isOpenRequest()){
			$this->addValidationMessage("There is no Request to reject");
		}
		
		$this->em->remove($userGroup);
	}
	
	/**
	 * The authenticated user leaves the current group
	 * 
	 * @param Group $group
	 */
	public function LeaveGroup(Group $group)
	{
		$userGroup = $this->Get($group);
		
		if($userGroup == null || !$userGroup->isOpen()){
			$this->addValidationMessage("You are not a Member of this group. You can not leave this Group");
		}
		
		$this->em->remove($userGroup);
	}
	
	/**
	 * The autheticated user invites the given user to the current Group
	 * 
	 * @param User $user
	 * @param $role
	 */
	public function InviteUser(User $user, $role = UserGroup::ROLE_MEMBER)
	{
		$group = $this->getContext()->getGroup();
		$userGroup = $this->get($group, $user);
		
		if($userGroup != null){
			$this->addValidationMessage("Membership or Membership Invitation exists allready");
		}
		
		$userGroup = new UserGroup();
		$userGroup->setGroup($group);
		$userGroup->setUser($user);
		$userGroup->setRequestedRole($role);
		$userGroup->acceptRequest($this->getContext()->getMe());
		
		$this->em->persist($userGroup);
	}
	
	/**
	 * Deletes the invitation of the given user to the current Group
	 * 
	 * @param User $user
	 */
	public function DeleteInvitation(User $user)
	{
		$group = $this->getContext()->getGroup();
		$userGroup = $this->Get($group, $user);
		
		if($userGroup == null || !$userGroup->isOpenInvitation()){
			$this->addValidationMessage("There is no Invitation to delete");
		}
		
		$this->em->remove($userGroup);
	}
	
	/**
	 * The authenticated user accepts the invitation to the given group
	 * 
	 * @param User $user
	 */
	public function AcceptInvitation(Group $group)
	{
		$userGroup = $this->Get($group);
		
		if($userGroup == null || !$userGroup->isOpenRequest()){
			$this->addValidationMessage("There is no Invitation to accept");
		}
		
		$userGroup->acceptInvitation();
	}
	
	/**
	 * The authenticated user rejects the invitation to the given group
	 *
	 * @param User $user
	 */
	public function RejectInvitation(Group $group)
	{
		$userGroup = $this->Get($group);
		
		if($userGroup == null || !$userGroup->isOpenRequest()){
			$this->addValidationMessage("There is no Invitation to accept");
		}
		
		$this->em->remove($userGroup);
	}
	
	/**
	 * The authenticated user kicks the given user out of the current group
	 * 
	 * @param User $user
	 */
	public function KickOutUser(User $user)
	{
		$group = $this->getContext()->getGroup();
		$userGroup = $this->Get($group, $user);
		
		if($userGroup == null || !$userGroup->isOpen()){
			$this->addValidationMessage("This user is not a Member of this Group. You can not kick him out!");
		}
		
		$this->em->remove($userGroup);
	}
	
	
}