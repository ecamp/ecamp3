<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\User;
use EcampCore\Entity\Group;
use EcampCore\Entity\UserGroup;
use EcampCore\Entity\Image;

class GroupTest extends \PHPUnit_Framework_TestCase
{
	
	public function testGroup()
	{
		$group = new Group();
		$pgroup = new Group();
		$image = new Image();
		$user = new User();
		$manager = new User();
		
		$pgroup->setImage($image);
		$this->assertEquals($image, $pgroup->getImage());
		
		$pgroup->delImage();
		$this->assertNull($pgroup->getImage());
		
		$group->setName('any group');
		$group->setDescription('any desc');
		$group->setImage($image);
		$group->setParent($pgroup);
		$pgroup->getChildren()->add($group);
		
		$usergroup = new UserGroup($manager, $group);
		$usergroup->setRequestedRole(UserGroup::ROLE_MANAGER);
		$usergroup->acceptInvitation();
		$usergroup->acceptRequest($manager);
		$group->getUserGroups()->add($usergroup);
		
		$usergroup = new UserGroup($user, $group);
		$usergroup->setRequestedRole(UserGroup::ROLE_MEMBER);
		$usergroup->acceptInvitation();
		$usergroup->acceptRequest($manager);
		$group->getUserGroups()->add($usergroup);
		
		$this->assertEquals('any group', $group->getName());
		$this->assertEquals('any desc', $group->getDescription());
		$this->assertEquals($image, $group->getImage());
		$this->assertEquals($pgroup, $group->getParent());
		$this->assertContains($pgroup, $group->getPathAsArray());
		
		$this->assertTrue($pgroup->hasChildren());
		$this->assertFalse($group->hasChildren());
		
		$this->assertTrue($group->isMember($user));
		$this->assertFalse($group->isManager($user));
		
		$this->assertFalse($group->isMember($manager));
		$this->assertTrue($group->isManager($manager));
		
		
		$this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $group->getCamps());
		$this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $group->getChildren());
		$this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $group->getMembers());
		$this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $group->getUserGroups());
		
	}
	
	
    
}
