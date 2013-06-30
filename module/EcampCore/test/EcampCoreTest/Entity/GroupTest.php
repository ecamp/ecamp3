<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\User;
use EcampCore\Entity\Group;
use EcampCore\Entity\UserGroup;
use EcampCore\Entity\Image;
use EcampCore\Entity\GroupMembership;

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

        $usergroup = GroupMembership::createRequest($manager, $group, GroupMembership::ROLE_MANAGER);
        $usergroup->acceptRequest($manager);

        $usergroup = GroupMembership::createRequest($user, $group, GroupMembership::ROLE_MEMBER);
        $usergroup->acceptRequest($manager);

        $this->assertEquals('any group', $group->getName());
        $this->assertEquals('any desc', $group->getDescription());
        $this->assertEquals($image, $group->getImage());
        $this->assertEquals($pgroup, $group->getParent());
        $this->assertContains($pgroup, $group->getPathAsArray());

        $this->assertTrue($pgroup->hasChildren());
        $this->assertFalse($group->hasChildren());

        $this->assertTrue($group->groupMembership()->isMember($user));
        $this->assertFalse($group->groupMembership()->isManager($user));

        $this->assertFalse($group->groupMembership()->isMember($manager));
        $this->assertTrue($group->groupMembership()->isManager($manager));

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $group->getCamps());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $group->getChildren());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $group->groupMembership()->getMembers());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $group->groupMembership()->getMembers());

    }

}
