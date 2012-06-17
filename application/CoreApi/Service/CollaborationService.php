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
use CoreApi\Entity\Camp;
use CoreApi\Entity\UserCamp;

class CollaborationService extends ServiceBase
{
	/**
	 * @var CoreApi\Service\UserService
	 * @Inject CoreApi\Service\UserService
	 */
	private $userService;
	
	/**
	 * @var CoreApi\Service\CampService
	 * @Inject CoreApi\Service\CampService
	 */
	private $campService;
	
	/**
	 * @var Core\Repository\UserCampRepository
	 * @Inject Core\Repository\UserCampRepository
	 */
	private $userCampRepo;
	
	public function Get($id, $camp_id = null){
		if($camp_id == null)
		{
			if(is_numeric($id)){
				return $this->userCampRepo->find($id);
			}
			if($id instanceof \CoreApi\Entity\UserCamp){
				return $id;
			}
		}
		else
		{
			$user = $this->userService->Get($id);
			$camp = $this->campService->Get($camp_id);
			
			return $this->userCampRepo->findOneBy(array(
				'user' => $user,
				'camp' => $camp
			));
		}
		
		return null;
	}
	
	
	
	public function GetCollaborators(Camp $camp){
		return $this->userCampRepo->findCollaboratorsByCamp($camp);
	}
	
	public function GetCamps(User $user){
		return $this->userCampRepo->findCollaboratorsByUser($user);
	}
	
	public function GetRequests(Camp $camp){
		return $this->userCampRepo->findOpenRequestsByCamp($camp);
	}
	
	public function GetInvitations(User $user){
		return $this->userCampRepo->findOpenInvitationsByUser($user);
	}
	
	public function RequestCollaboration(Camp $camp, $role = UserCamp::ROLE_MEMBER){
		$collaboration = new UserCamp();
		$this->persist($collaboration);

		$collaboration->setCamp($camp);
		$collaboration->setUser($this->getContext()->getMe());
		$collaboration->setRequestedRole($role);
		$collaboration->acceptInvitation();
		
		return $collaboration;
	}
	
	public function DeleteRequest(UserCamp $request)
	{
		$this->validationAssert(
			$request->isOpenRequest());
		
		$this->validationAssert(
			$request->getUser() == $this->getContext()->getMe());
		
		$this->remove($request);
	}
	
	public function AcceptRequest(UserCamp $request)
	{
		$this->validationAssert(
			$request->isOpenRequest());
		
		$this->validationAssert(
			$request->getCamp()->isManager($this->getContext()->getMe()));
		
		$request->acceptRequest($this->getContext()->getMe());
	}
	
	public function RejectRequest(UserCamp $request)
	{
		$this->validationAssert(
			$request->isOpenRequest());
		
		$this->validationAssert(
			$request->getCamp()->isManager($this->getContext()->getMe()));
		
		$this->remove($request);
	}
	
	public function LeaveCamp(Camp $camp)
	{
		$me = $this->getContext()->getMe();
		$collaboration = $this->Get($me, $camp);
		
		$this->remove($collaboration);
	}
	
	public function InviteUser(User $user, $role = UserCamp::ROLE_MEMBER)
	{
		$collaboration = new UserCamp();
		$this->persist($collaboration);
		
		$collaboration->setCamp($this->getContext()->getCamp());
		$collaboration->setUser($user);
		$collaboration->setRequestedRole($role);
		$collaboration->acceptRequest($this->getContext()->getMe());
		
		return $collaboration;
	}
	
	public function DeleteInvitation(UserCamp $invitation)
	{
		$this->validationAssert(
			$invitation->isOpenInvitation());
		
		$this->validationAssert(
			$invitation->getCamp()->isManager($this->getContext()->getMe()));
		
		$this->remove($invitation);
	}
	
	public function AcceptInvitation(UserCamp $invitation)
	{
		$this->validationAssert(
			$invitation->isOpenInvitation());
		
		$this->validationAssert(
			$invitation->getUser() == $this->getContext()->getMe());
		
		$invitation->acceptInvitation();
	}
	
	public function RejectInvitation(UserCamp $invitation)
	{
		$this->validationAssert(
			$invitation->isOpenInvitation());
		
		$this->validationAssert(
			$invitation->getUser() == $this->getContext()->getMe());
		
		$this->remove($invitation);
	}
	
	public function KickOutUser(User $user)
	{
		$camp = $this->getContext()->getCamp();
		$collaboration = $this->Get($user, $camp);
		
		$this->remove($collaboration);
	}
}