<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\CampType;
use eCamp\Core\Entity\Event;
use eCamp\Core\Entity\EventCategory;
use eCamp\Core\Entity\EventTemplate;
use eCamp\Core\Entity\EventType;
use eCamp\Core\Entity\EventTypeFactory;
use eCamp\Core\Entity\EventTypePlugin;
use eCamp\Core\Entity\Organization;
use eCamp\Core\Entity\Plugin;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

class OrganizationTest extends AbstractTestCase
{
    public function testOrganization()
    {
        $organization = new Organization();
        $organization->setName('OrganizationName');

        $this->assertEquals('OrganizationName', $organization->getName());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $organization->getCampTypes());
    }

    public function testCampTypes()
    {
        $campType = new CampType();
        $organization = new Organization();

        $this->assertEquals(0, $organization->getCampTypes()->count());
        $organization->addCampType($campType);
        $this->assertContains($campType, $organization->getCampTypes());
        $organization->removeCampType($campType);
        $this->assertEquals(0, $organization->getCampTypes()->count());
    }

}