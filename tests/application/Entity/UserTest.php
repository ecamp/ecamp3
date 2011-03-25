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

namespace Entity;

class UserTest
	extends \EcampTestCaseWithDb
{

	function setUp()
	{
		parent::setup();
	}

	function testCanCreateUser()
	{
		$this->assertInstanceOf('Entity\User', new User());
	}

	function testCanPersistUser(){
		$u = new User();
		$u->setScoutname('Scout');

		$this->em->persist($u);
		$this->em->flush();

		$users = $this->em->getRepository("Entity\User")->findAll();

		$this->assertEquals('Scout', $users[0]->getScoutname());
	}



	function testCanSetProperties()
	{
		$user = new User();


		$user->setScoutname('ScoutName');
		$this->assertEquals('ScoutName', $user->getScoutname());


		$user->setFirstname('FirstName');
		$this->assertEquals('FirstName', $user->getFirstname());
	}

	function testCanCreateFriendship()
	{
		$user1 =  new User();
		$user2 =  new User();

		/* no friends */
		$this->assertFalse( $user1->isFriendOf($user2) );
		$this->assertFalse( $user2->isFriendOf($user1) );

		/* send invitation from user1 to user2 */
		$rel1 = new UserRelationship($user1, $user2);
		$user1->getRelationshipFrom()->add($rel1);
		$user2->getRelationshipTo()->add($rel1);

		$this->assertFalse( $user1->isFriendOf($user2) );
		$this->assertFalse( $user2->isFriendOf($user1) );

		$this->assertTrue( $user1->sentFriendshipRequestTo($user2) );
		$this->assertTrue( $user2->receivedFriendshipRequestFrom($user1) );

		$this->assertFalse( $user2->sentFriendshipRequestTo($user1) );
		$this->assertFalse( $user1->receivedFriendshipRequestFrom($user2) );

		/* user 2 accepts invitation */
		$rel2 = new UserRelationship($user2, $user1);
		$user2->getRelationshipFrom()->add($rel2);
		$user1->getRelationshipTo()->add($rel2);

		$this->assertTrue( $user1->isFriendOf($user2) );
		$this->assertTrue( $user2->isFriendOf($user1) );

		$this->assertFalse( $user1->sentFriendshipRequestTo($user2) );
		$this->assertFalse( $user2->receivedFriendshipRequestFrom($user1) );

	}

}