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

use CoreApi\Entity\UserGroup;

use CoreApi\Entity\User;
use CoreApi\Entity\Group;

use CoreApi\Service\Params\ArrayParams;
use CoreApi\Entity\UserRelationship;
use ServiceTestCase;

class MembershipServiceTest extends ServiceTestCase
{
	
	/**
	 * @var CoreApi\Service\UserService
	 * @Inject CoreApi\Service\UserService
	 */
	private $userService;
	
	/**
	 * @var CoreApi\Service\GroupService
	 * @Inject CoreApi\Service\GroupService
	 */
	private $groupService;
	
	/**
	 * @var CoreApi\Service\MembershipService
	 * @Inject CoreApi\Service\MembershipService
	 */
	private $membershipService;
	
	/**
	 * @var Core\Acl\DefaultAcl
	 * @Inject Core\Acl\DefaultAcl
	 */
	private $acl;
	
	
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
	
	
	private static $groupCounter = 0;
	private function createGroup(User $manager){
		$this->em->getConnection()->beginTransaction();
		
		self::$groupCounter++;
		$groupname = 'testgroup' . self::$groupCounter;
		
		$group = new Group();
		$group->setName($groupname);
		$group->setDescription("UnitTestGroup");
		
		$this->em->persist($group);
		
		$ug = new UserGroup($manager, $group);
		$ug->setRequestedRole(UserGroup::ROLE_MANAGER);
		$ug->acceptRequest($manager);
		$ug->acceptInvitation();
		
		$this->em->persist($ug);
		$this->em->flush();
		$this->em->getConnection()->commit();
		
		return $group;
	}
	
	
	
	
	public function testCanGetUserGroupById(){
		
		$manager = $this->createUser();
		$group = $this->createGroup($manager);
		
		$this->defineContext($manager, null, null, null);
		
		$ug = $this->membershipService->Get($group);
		
		$this->assertEquals($ug, $this->membershipService->Get($ug->getId()));
	}
	
	public function testCanGetUserGroupEntity(){
		
		$user = $this->createUser();
		$manager = $this->createUser();
		$group = $this->createGroup($manager);
		
		$this->defineContext($user, null, null, null);
		
		$ug = $this->membershipService->Get($group, $user);
		$this->assertEmpty($ug);
		
		
		$ug = $this->membershipService->Get($group, $manager);
		
		$this->assertNotNull($ug);
		$this->assertEquals($manager, $ug->getUser());
		$this->assertEquals($group, $ug->getGroup());
		
	}
	
	
	public function testCanGetMembersAndGetGroups(){
		$user = $this->createUser();
		
		$manager = $this->createUser();
		$group = $this->createGroup($manager);
		
		$this->defineContext($user, null, null, null);
		
		
		$ugs = $this->membershipService->GetMembers($group);
		$ids = array();
		
		foreach($ugs as $ug){	$ids[] = $ug->getUser()->getId();	}
		$this->assertContains($manager->getId(), $ids, "Membership not created");
		
		
		$ugs = $this->membershipService->GetGroups($manager);
		$ids = array();
		
		foreach($ugs as $ug){	$ids[] = $ug->getGroup()->getId();	}
		$this->assertContains($group->getId(), $ids);
	}
	
	
	public function testCanRequestMembershipAndGetRequests(){
		$user = $this->createUser();
		
		$manager = $this->createUser();
		$group = $this->createGroup($manager);
		
		$this->defineContext($user, null, null, null);
		$this->membershipService->RequestMembership($group);
		
		$this->defineContext($manager, null, $group, null);
		$ugs = $this->membershipService->GetRequests();
		
		$ug = $this->membershipService->Get($group, $user);
		
		$this->assertNotNull($ug);
		$this->assertTrue($ug->isOpen());
		$this->assertTrue(in_array($ug, $ugs));

		
		$this->setExpectedException("Core\Service\ValidationException");
		$this->defineContext($user, null, null, null);
		$this->membershipService->RequestMembership($group);
	} 
	
	
	public function testCanInviteUserAndGetInvitations(){
		$user = $this->createUser();
		
		$manager = $this->createUser();
		$group = $this->createGroup($manager);
		
		$this->defineContext($manager, null, $group, null);
		$this->membershipService->InviteUser($user);
		
		$this->defineContext($user, null, null, null);
		$ugs = $this->membershipService->GetInvitations();
		
		$ug = $this->membershipService->Get($group, $user);
		
		$this->assertNotNull($ug);
		$this->assertTrue($ug->isOpen());
		$this->assertTrue(in_array($ug, $ugs));
		
		
		$this->setExpectedException("Core\Service\ValidationException");
		$this->defineContext($manager, null, $group, null);
		$this->membershipService->InviteUser($user);
	}
	
	
	public function testCanDeleteMembershipRequest(){
		$user = $this->createUser();
		
		$manager = $this->createUser();
		$group = $this->createGroup($manager);
		
		$this->defineContext($user, null, null, null);
		$this->membershipService->RequestMembership($group);
		
		$ug = $this->membershipService->Get($group, $user);
		$this->assertNotNull($ug);
		
		$this->membershipService->DeleteRequest($group);
		
		$ug = $this->membershipService->Get($group, $user);
		$this->assertNull($ug);
		
		
		$this->setExpectedException("Core\Service\ValidationException");
		$this->membershipService->DeleteRequest($group);
	}
	
	
	public function testCanDeleteMembershipInvitation(){
		$user = $this->createUser();
		
		$manager = $this->createUser();
		$group = $this->createGroup($manager);
		
		$this->defineContext($manager, null, $group, null);
		$this->membershipService->InviteUser($user);
		
		$ug = $this->membershipService->Get($group, $user);
		$this->assertNotNull($ug);
		
		$this->membershipService->DeleteInvitation($user);
		
		$ug = $this->membershipService->Get($group, $user);
		$this->assertNull($ug);
		
		
		$this->setExpectedException("Core\Service\ValidationException");
		$this->membershipService->DeleteInvitation($user);
	}
	
	public function testCanAcceptMembershipRequest(){
		$user = $this->createUser();
		
		$manager = $this->createUser();
		$group = $this->createGroup($manager);
		
		$this->defineContext($user, null, null, null);
		$this->membershipService->RequestMembership($group);
		
		$this->defineContext($manager, null, $group, null);
		$this->membershipService->AcceptRequest($user);
		
		$ug = $this->membershipService->Get($group, $user);
		$this->assertNotNull($ug);
		$this->assertFalse($ug->isOpen());
		
		
		$this->setExpectedException("Core\Service\ValidationException");
		$this->membershipService->AcceptRequest($user);
	}
	
	
	public function testCanAcceptMembershipInvitation(){
		$user = $this->createUser();
		
		$manager = $this->createUser();
		$group = $this->createGroup($manager);
		
		$this->defineContext($manager, null, $group, null);
		$this->membershipService->InviteUser($user);
		
		$this->defineContext($user, null, null, null);
		$this->membershipService->AcceptInvitation($group);
		
		$ug = $this->membershipService->Get($group, $user);
		$this->assertNotNull($ug);
		$this->assertFalse($ug->isOpen());
		
		
		$this->setExpectedException("Core\Service\ValidationException");
		$this->membershipService->AcceptInvitation($group);
	}
	
	
	public function testCanRejectMembershipRequest(){
		$user = $this->createUser();
		
		$manager = $this->createUser();
		$group = $this->createGroup($manager);
		
		$this->defineContext($user, null, null, null);
		$this->membershipService->RequestMembership($group);
		
		$this->defineContext($manager, null, $group, null);
		$this->membershipService->RejectRequest($user);
		
		$ug = $this->membershipService->Get($group, $user);
		$this->assertNull($ug);
		
		
		$this->setExpectedException("Core\Service\ValidationException");
		$this->membershipService->RejectRequest($user);
	}
	
	
	public function testCanRejectMembershipInvitation(){
		$user = $this->createUser();
		
		$manager = $this->createUser();
		$group = $this->createGroup($manager);
		
		$this->defineContext($manager, null, $group, null);
		$this->membershipService->InviteUser($user);
		
		$this->defineContext($user, null, null, null);
		$this->membershipService->RejectInvitation($group);
		
		$ug = $this->membershipService->Get($group, $user);
		$this->assertNull($ug);
		
		
		$this->setExpectedException("Core\Service\ValidationException");
		$this->membershipService->RejectInvitation($group);
	}
	
	public function testCanLeaveGroup(){
		$manager = $this->createUser();
		$group = $this->createGroup($manager);
		
		$this->defineContext($manager, null, $group, null);
		$this->membershipService->LeaveGroup();
		
		$ug = $this->membershipService->Get($group, $manager);
		$this->assertNull($ug);
		
		
		$this->setExpectedException("Core\Service\ValidationException");
		$this->membershipService->LeaveGroup();
	}
	
	public function testCanKickUser(){
		$user = $this->createUser();
		
		$manager = $this->createUser();
		$group = $this->createGroup($manager);
		
		$this->defineContext($user, null, null, null);
		$this->membershipService->RequestMembership($group);
		
		$this->defineContext($manager, null, $group, null);
		$this->membershipService->AcceptRequest($user);
		
		$ug = $this->membershipService->Get($group, $user);
		$this->assertNotNull($ug);
		
		$this->membershipService->KickOutUser($user);
		
		$ug = $this->membershipService->Get($group, $user);
		$this->assertNull($ug);
		
		
		$this->setExpectedException("Core\Service\ValidationException");
		$this->membershipService->KickOutUser($user);
	}
	
	
	
}