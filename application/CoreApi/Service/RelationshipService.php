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

class RelationshipService extends ServiceBase
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
	 * @var Core\Repository\UserRelationshipRepository
	 * @Inject Core\Repository\UserRelationshipRepository
	 */
	private $userRelationshipRepo;
	
	
	public function _setupAcl(){
		
	}
	
	
	public function Get($id, $user_id = null){
		if($user_id == null){
			if(is_numeric($id))
				$this->userRelationshipRepo->find($id);
			
			if($id instanceof \CoreApi\Entity\UserRelationship)
				return $id;
		}
		else{
			$user1 = $this->userService->Get($id);
			$user2 = $this->campService->Get($user_id);
			
			return $this->userRelationshipRepo->findByUsers($user1, $user2);
		}
		
		return null;
	}
	
	
	
	public function GetRequests(){
		
	}
	
	public function GetInvitations(){
		
	}
	
	
	public function RequestRelationship(){
		
	}
	
	public function DeleteRequest(){
		
	}
	
	public function AcceptInvitation(){
		
	}
	
	public function RejectInvitation(){
		
	}
	
}