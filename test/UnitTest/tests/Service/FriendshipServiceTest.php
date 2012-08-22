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

namespace ServiceTest;

use CoreApi\Service\Params\ArrayParams;
use CoreApi\Entity\UserRelationship;
use ServiceTestCase;

class FriendshipServiceTest extends ServiceTestCase
{
	
	
	/**
	 * @var CoreApi\Service\UserService
	 * @Inject CoreApi\Service\UserService
	 */
	private $userService;
	
	/**
	 * @var CoreApi\Service\RelationshipService
	 * @Inject CoreApi\Service\RelationshipService
	 */
	private $relationshipService;
	
	
	public function setUp()
	{
		parent::setUp();
		
// 		$this->loadDatabaseDump("loginServiceTest.sql");
// 		$this->defineContext(2, 2, 4, 5);
	}
	
	private static $userCounter = 0;
	private function getUserData()
	{
		self::$userCounter++;
		$username ='testuser' . self::$userCounter;
		
		return array
		(	'username' 		=> $username
		,	'email' 		=> $username . '@user.ch'
		,	'scoutname'		=> 'scout-name' . self::$userCounter
		,	'firstname'		=> 'first-name' . self::$userCounter
		,	'surname'		=> 'sur-name' . self::$userCounter
		,	'password'		=> 'password' . self::$userCounter
		,	'passwordCheck'	=> 'password' . self::$userCounter
		);
	}
	
	private function createUser(){
		return $this->userService->Create(new ArrayParams($this->getUserData()));
	}
	
	
	private function createFriendPair(){
		$user1 = $this->createUser();
		$user2 = $this->createUser();
		
		$ur1 = new UserRelationship($user1, $user2);
		$ur2 = new UserRelationship($user1, $user2);
		
		UserRelationship::Link($ur1, $ur2);
		
		$this->em->persist($ur1);
		$this->em->persist($ur2);
		
		return array($ur1, ur2);
	}
	
	
	public function testCanRequestFriendship()
	{
		$user1 = $this->createUser();
		$user2 = $this->createUser();
		
		$this->defineContext($user1->getId(), null, null, null);
		
		$this->relationshipService->RequestRelationship($user2);
		
		$urs = $this->relationshipService->GetRequests();
		$ids = array();
		
		foreach($urs as $ur){	$ids[] = $ur->getTo()->getId();	}
		$this->assertContains($user2->getId(), $ids, "Request not created");
		
		
		
		$this->defineContext($user2->getId(), null, null, null);
		
		$urs = $this->relationshipService->GetInvitations();
		$ids = array();
		
		foreach($urs as $ur){	$ids[] = $ur->getFrom()->getId();	}
		$this->assertContains($user1->getId(), $ids, "Invitatino not received");
	}
	
	
	public function testCanAcceptFriendshipRequest()
	{
		$user1 = $this->createUser();
		$user2 = $this->createUser();
		
		$this->defineContext($user1->getId(), null, null, null);
		$this->relationshipService->RequestRelationship($user2);
		
		$this->defineContext($user2->getId(), null, null, null);
		$this->relationshipService->AcceptInvitation($user1);
		
		
		$ur1 = $this->relationshipService->Get($user1, $user2);
		$ur2 = $this->relationshipService->Get($user2, $user1);
		
		$this->assertNotNull($ur1);
		$this->assertNotNull($ur2);
		
		$this->assertEquals($ur1->getCounterpart(), $ur2);
		$this->assertEquals($ur2->getCounterpart(), $ur1);
	}
	
	
	public function testCanRejectFriendshipRequest()
	{
		$user1 = $this->createUser();
		$user2 = $this->createUser();
		
		$this->defineContext($user1->getId(), null, null, null);
		$this->relationshipService->RequestRelationship($user2);
		
		$this->defineContext($user2->getId(), null, null, null);
		$this->relationshipService->RejectInvitation($user1);
		
		$ur1 = $this->relationshipService->Get($user1, $user2);
		$ur2 = $this->relationshipService->Get($user2, $user1);
		
		$this->assertNull($ur1);
		$this->assertNull($ur2);
	}
	
	
	public function testCanDeleteFreindshipRequest()
	{
		$user1 = $this->createUser();
		$user2 = $this->createUser();
		
		$this->defineContext($user1->getId(), null, null, null);
		$this->relationshipService->RequestRelationship($user2);
		
		
		$ur = $this->relationshipService->Get($user1, $user2);
		$this->assertNotNull($ur);
		
		$this->defineContext($user1->getId(), null, null, null);
		$this->relationshipService->DeleteRequest($user2);
		
		$ur1 = $this->relationshipService->Get($user1, $user2);
		$ur2 = $this->relationshipService->Get($user2, $user1);
		
		$this->assertNull($ur1);
		$this->assertNull($ur2);
	}
	
	
	public function testCanCancelFreindship()
	{
		$user1 = $this->createUser();
		$user2 = $this->createUser();
		
		$this->defineContext($user1->getId(), null, null, null);
		$this->relationshipService->RequestRelationship($user2);
		
		$this->defineContext($user2->getId(), null, null, null);
		$this->relationshipService->AcceptInvitation($user1);
		
		$this->defineContext($user1->getId(), null, null, null);
		$this->relationshipService->CancelRelationship($user2);
		
		$ur1 = $this->relationshipService->Get($user1, $user2);
		$ur2 = $this->relationshipService->Get($user2, $user1);
		
		$this->assertNull($ur1);
		$this->assertNull($ur2);
	}
	
	
	public function testCanGetUserRelationship()
	{
		$user1 = $this->createUser();
		$user2 = $this->createUser();
		
		$this->defineContext($user1->getId(), null, null, null);
		$this->relationshipService->RequestRelationship($user2);
		
		$this->defineContext($user2->getId(), null, null, null);
		$this->relationshipService->AcceptInvitation($user1);
		
		$ur = $this->relationshipService->Get($user1, $user2);
		
		$this->assertNotNull($ur);
		$this->assertEquals($ur, $this->relationshipService->Get($ur));
		$this->assertEquals($ur, $this->relationshipService->Get($ur->getId()));
	}
}