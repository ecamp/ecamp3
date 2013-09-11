<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\CampType;
use EcampCore\Entity\Camp;
use EcampCore\Entity\User;
use EcampCore\Entity\Group;
use EcampCore\Entity\CampCollaboration;

class CampTest extends \PHPUnit_Framework_TestCase
{

    public function testUserCamp()
    {
        $campType = new CampType('name', 'type');
        $user = new User();

        $camp = new Camp($campType);
        $camp->setCreator($user);
        $camp->setOwner($user);

        $campCollaboration = CampCollaboration::createRequest($user, $camp, CampCollaboration::ROLE_MEMBER);
        $campCollaboration->acceptRequest($user);

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

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $camp->campCollaboration()->getMembers());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $camp->campCollaboration()->getManagers());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $camp->getPeriods());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $camp->getEvents());

        $this->assertContains($user, $camp->campCollaboration()->getMembers());
        $this->assertNotContains($user, $camp->campCollaboration()->getManagers());

        $this->assertTrue($camp->campCollaboration()->isMember($user));
        $this->assertFalse($camp->campCollaboration()->isManager($user));
    }

    public function testGroupCamp()
    {
        $campType = new CampType('name', 'type');

        $user = new User();
        $group = new Group();

        $camp = new Camp($campType);
        $camp->setCreator($user);
        $camp->setGroup($group);

        $campCollaboration = CampCollaboration::createRequest($user, $camp, CampCollaboration::ROLE_MANAGER);
        $campCollaboration->acceptRequest($user);

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

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $camp->campCollaboration()->getMembers());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $camp->campCollaboration()->getManagers());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $camp->getPeriods());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $camp->getEvents());

        $this->assertNotContains($user, $camp->campCollaboration()->getMembers());
        $this->assertContains($user, $camp->campCollaboration()->getManagers());

        $this->assertFalse($camp->campCollaboration()->isMember($user));
        $this->assertTrue($camp->campCollaboration()->isManager($user));
    }

}
