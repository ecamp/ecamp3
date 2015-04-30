<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\CampType;
use EcampCore\Entity\Camp;
use EcampCore\Entity\Day;
use EcampCore\Entity\Period;
use EcampCore\Entity\User;
use EcampCore\Entity\Group;
use EcampCore\Entity\CampCollaboration;

class CampTest extends \PHPUnit_Framework_TestCase
{

    public static function createUserCamp()
    {
        $camp = new Camp();
        $camp->setCampType(CampTypeTest::createCampType());
        $camp->setCreator(UserTest::createUser());
        $camp->setOwner(UserTest::createUser());

        $camp->setName("Camp.Name");
        $camp->setTitle("Camp.Title");
        $camp->setMotto("Camp.Motto");
        $camp->setVisibility(Camp::VISIBILITY_PUBLIC);

        $camp->prePersist();

        return $camp;
    }

    public static function createGroupCamp()
    {
        $camp = new Camp();
        $camp->setCampType(CampTypeTest::createCampType());
        $camp->setCreator(UserTest::createUser());
        $camp->setOwner(GroupTest::createGroup());

        $camp->setName("Camp.Name");
        $camp->setTitle("Camp.Title");
        $camp->setMotto("Camp.Motto");
        $camp->setVisibility(Camp::VISIBILITY_CONTRIBUTORS);

        $camp->prePersist();

        return $camp;
    }

    public function testUserCamp()
    {
        $camp = self::createUserCamp();
        $user = UserTest::createUser();

        $this->assertContains($camp, $camp->getOwner()->getOwnedCamps());

        $campCollaboration = CampCollaboration::createRequest($user, $camp, CampCollaboration::ROLE_MEMBER);
        $campCollaboration->acceptRequest($camp->getCreator());
        $campCollaboration->PrePersist();

        $this->assertEquals('Camp.Name', $camp->getName());
        $this->assertEquals('Camp.Title', $camp->getTitle());
        $this->assertEquals('Camp.Motto', $camp->getMotto());

        $this->assertEquals("CampType.Name", $camp->getCampType()->getName());
        $this->assertEquals("User.Username", $camp->getCreator()->getUsername());
        $this->assertEquals("User.Scoutname", $camp->getOwner()->getDisplayName());
        $this->assertTrue($camp->belongsToUser());
        $this->assertFalse($camp->belongsToGroup());

        $this->assertEquals(Camp::VISIBILITY_PUBLIC, $camp->getVisibility());

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $camp->campCollaboration()->getMembers());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $camp->campCollaboration()->getManagers());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $camp->getEventCategories());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $camp->getPeriods());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $camp->getEvents());

        $this->assertContains($user, $camp->campCollaboration()->getMembers());
        $this->assertNotContains($user, $camp->campCollaboration()->getManagers());

        $this->assertTrue($camp->campCollaboration()->isMember($user));
        $this->assertFalse($camp->campCollaboration()->isManager($user));
    }

    public function testGroupCamp()
    {
        $camp = self::createGroupCamp();
        $user = UserTest::createUser();

        $this->assertContains($camp, $camp->getOwner()->getOwnedCamps());

        $campCollaboration = CampCollaboration::createRequest($user, $camp, CampCollaboration::ROLE_MANAGER);
        $campCollaboration->acceptRequest($user);
        $campCollaboration->PrePersist();

        $this->assertEquals('Camp.Name', $camp->getName());
        $this->assertEquals('Camp.Title', $camp->getTitle());
        $this->assertEquals('Camp.Motto', $camp->getMotto());

        $this->assertEquals("CampType.Name", $camp->getCampType()->getName());
        $this->assertEquals("User.Username", $camp->getCreator()->getUsername());
        $this->assertEquals("Group.Name", $camp->getOwner()->getDisplayName());
        $this->assertFalse($camp->belongsToUser());
        $this->assertTrue($camp->belongsToGroup());

        $this->assertEquals(Camp::VISIBILITY_CONTRIBUTORS, $camp->getVisibility());

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $camp->campCollaboration()->getMembers());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $camp->campCollaboration()->getManagers());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $camp->getEventCategories());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $camp->getPeriods());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $camp->getEvents());

        $this->assertNotContains($user, $camp->campCollaboration()->getMembers());
        $this->assertContains($user, $camp->campCollaboration()->getManagers());

        $this->assertFalse($camp->campCollaboration()->isMember($user));
        $this->assertTrue($camp->campCollaboration()->isManager($user));
    }

    public function testStartEnd()
    {
        $camp = self::createUserCamp();

        $pStart1 = \DateTime::createFromFormat('j-M-Y H:i:s', '1-Jan-2000 00:00:00');
        $pStart2 = \DateTime::createFromFormat('j-M-Y H:i:s', '31-Jan-2000 00:00:00');
        $pStart3 = \DateTime::createFromFormat('j-M-Y H:i:s', '31-Dec-2000 00:00:00');

        $period = new Period($camp);
        $period->getDays()->add(new Day($period, 0));
        $period->getDays()->add(new Day($period, 1));

        $this->assertEquals(null, $camp->getStart());
        $this->assertEquals(null, $camp->getEnd());
        $this->assertEquals('-', $camp->getRange());

        $period->prePersist();

        $period->setStart($pStart1);
        $this->assertEquals('01. - 02.01.2000', $camp->getRange());

        $period->setStart($pStart2);
        $this->assertEquals('31.01. - 01.02.2000', $camp->getRange());

        $period->setStart($pStart3);
        $this->assertEquals('31.12.2000 - 01.01.2001', $camp->getRange());
    }

    /**
     * @expectedException \Exception
     */
    public function testVisibility()
    {
        $camp = self::createUserCamp();
        $camp->setVisibility('unallowed value');
    }

    public function testPreRemove()
    {
        $camp = self::createUserCamp();
        $camp->preRemove();

        $this->assertNotContains($camp, $camp->getOwner()->getOwnedCamps());
    }

    public function testResourceId()
    {
        $camp = self::createUserCamp();
        $this->assertInstanceOf($camp->getResourceId(), $camp);
    }

}
