<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\Group;
use EcampCore\Entity\Image;

class GroupTest extends \PHPUnit_Framework_TestCase
{

    public static function createParentGroup($name = null)
    {
        $group = new Group();
        $group->setName($name ?: "Group.ParentName");
        $group->setDescription("Group.ParentDescription");

        return $group;
    }

    public static function createGroup()
    {
        $group = new Group();
        $group->setName("Group.Name");
        $group->setDescription("Group.Description");

        return $group;
    }


    public function testNewGroup()
    {
        $group = self::createGroup();

        $this->assertEquals('Group.Name', $group->getName());
        $this->assertEquals('Group.Description', $group->getDescription());

        $this->assertEquals('EcampCore\Entity\Group', $group->getResourceId());
        $this->assertNotNull($group->groupMembership());
    }

    public function testParentGroup()
    {
        $group = self::createGroup();
        $parentGroupA = self::createParentGroup("Group.ParentName.A");
        $parentGroupB = self::createParentGroup("Group.ParentName.B");

        $group->setParent($parentGroupA);
        $this->assertEquals($parentGroupA, $group->getParent());
        $this->assertContains($group, $parentGroupA->getChildren());
        $this->assertNotContains($group, $parentGroupB->getChildren());
        $this->assertTrue($parentGroupA->hasChildren());
        $this->assertFalse($parentGroupB->hasChildren());
        $this->assertEquals('Group.ParentName.A > Group.Name', $group->getPath());

        $group->setParent($parentGroupB);
        $this->assertEquals($parentGroupB, $group->getParent());
        $this->assertContains($group, $parentGroupB->getChildren());
        $this->assertNotContains($group, $parentGroupA->getChildren());
        $this->assertTrue($parentGroupB->hasChildren());
        $this->assertFalse($parentGroupA->hasChildren());
        $this->assertEquals('Group.ParentName.B > Group.Name', $group->getPath());

        $group->preRemove();
        $this->assertNull($group->getParent());
        $this->assertNotContains($group, $parentGroupA->getChildren());
        $this->assertNotContains($group, $parentGroupB->getChildren());

    }

    public function testGroupImage()
    {
        $image = new Image();

        $group = self::createGroup();

        $group->setImage($image);
        $this->assertEquals($image, $group->getImage());

        $group->delImage();
        $this->assertNull($group->getImage());
    }
}
