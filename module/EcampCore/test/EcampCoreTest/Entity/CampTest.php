<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\CampType;
use EcampCore\Entity\Camp;
use EcampCore\Entity\User;
use EcampCore\Entity\Group;
use EcampCore\Entity\UserCamp;

class CampTest extends \PHPUnit_Framework_TestCase
{

	public function testUserCamp()
	{
		$campType = new CampType();
		$campType->setName('any camp type');
		$campType->setType('any type');
		
		$user = new User();
		
		$camp = new Camp($campType);
		$camp->setCreator($user);
		$camp->setOwner($user);
		
		$usercamp = new UserCamp($user, $camp);
		$usercamp->setRequestedRole(UserCamp::ROLE_MEMBER);
		$usercamp->acceptInvitation();
		$usercamp->acceptRequest($user);
		$camp->getUserCamps()->add($usercamp);
		
		$camp->setName('any camp name');
		$camp->setTitle('any camp title');
		
		
		$this->assertEquals($campType, $camp->getCampType());
		$this->assertEquals($user, $camp->getCreator());
		$this->assertEquals($user, $camp->getOwner());
		$this->assertNull($camp->getGroup());
		$this->assertTrue($camp->belongsToUser());
		$this->assertEquals('any camp name', $camp->getName());
		$this->assertEquals('any camp title', $camp->getTitle());
		
		$this->assertEquals(Camp::VISIBILITY_PUBLIC, $camp->getVisibility());
		
		$this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $camp->getMembers());
		$this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $camp->getManagers());
		$this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $camp->getPeriods());
		$this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $camp->getEvents());
	
		$this->assertContains($user, $camp->getMembers());
		$this->assertNotContains($user, $camp->getManagers());
		
		$this->assertTrue($camp->isMember($user));
		$this->assertFalse($camp->isManager($user));
	}
	
	
	public function testGroupCamp()
	{
		$campType = new CampType();
		$campType->setName('any camp type');
		$campType->setType('any type');
		
		$user = new User();
		$group = new Group();
		
		$camp = new Camp($campType);
		$camp->setCreator($user);
		$camp->setGroup($group);
		
		$usercamp = new UserCamp($user, $camp);
		$usercamp->setRequestedRole(UserCamp::ROLE_MANAGER);
		$usercamp->acceptInvitation();
		$usercamp->acceptRequest($user);
		$camp->getUserCamps()->add($usercamp);
		
		$camp->setName('any camp name');
		$camp->setTitle('any camp title');
		$camp->setVisibility(Camp::VISIBILITY_CONTRIBUTORS);
		
		$this->assertEquals($campType, $camp->getCampType());
		$this->assertEquals($user, $camp->getCreator());
		$this->assertEquals($group, $camp->getGroup());
		$this->assertNull($camp->getOwner());
		$this->assertFalse($camp->belongsToUser());
		$this->assertEquals('any camp name', $camp->getName());
		$this->assertEquals('any camp title', $camp->getTitle());
		
		$this->assertEquals(Camp::VISIBILITY_CONTRIBUTORS, $camp->getVisibility());
		
		$this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $camp->getMembers());
		$this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $camp->getManagers());
		$this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $camp->getPeriods());
		$this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $camp->getEvents());

		$this->assertNotContains($user, $camp->getMembers());
		$this->assertContains($user, $camp->getManagers());

		$this->assertFalse($camp->isMember($user));
		$this->assertTrue($camp->isManager($user));
	}
    
}
