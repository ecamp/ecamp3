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

class UserTest
	extends PHPUnit_Framework_TestCase
{

	private $em;

	function setUp()
	{
		$this->em = $GLOBALS['bootstrap']->em;
	}


	function testCreateUser()
	{
		$user = new Entity\User();

		$this->assertTrue(true);

	}

	function testProperties()
	{
		$user = new Entity\User();


		$user->setScoutname('ScoutName');
		$this->assertEquals('ScoutName', $user->getScoutname());


		$user->setFirstname('FirstName');
		$this->assertEquals('FirstName', $user->getFirstname());
	}

	function testFriendship()
	{
		$user1 =  new Entity\User();
		$user2 =  new Entity\User();

		/* no friends */
		$this->assertFalse( $user1->isFriendOf($user2) );
		$this->assertFalse( $user2->isFriendOf($user1) );

		/* send invitation from user1 to user2 */
		$rel1 = new Entity\UserRelationship($user1, $user2);
		$user1->getRelationshipFrom()->add($rel1);
		$user2->getRelationshipTo()->add($rel1);

		$this->assertFalse( $user1->isFriendOf($user2) );
		$this->assertFalse( $user2->isFriendOf($user1) );

		$this->assertTrue( $user1->sentFriendshipRequestTo($user2) );
		$this->assertTrue( $user2->receivedFriendshipRequestFrom($user1) );

		$this->assertFalse( $user2->sentFriendshipRequestTo($user1) );
		$this->assertFalse( $user1->receivedFriendshipRequestFrom($user2) );

		/* user 2 accepts invitation */
		$rel2 = new Entity\UserRelationship($user2, $user1);
		$user2->getRelationshipFrom()->add($rel2);
		$user1->getRelationshipTo()->add($rel2);
		
		$this->assertTrue( $user1->isFriendOf($user2) );
		$this->assertTrue( $user2->isFriendOf($user1) );

		$this->assertFalse( $user1->sentFriendshipRequestTo($user2) );
		$this->assertFalse( $user2->receivedFriendshipRequestFrom($user1) );

	}

}