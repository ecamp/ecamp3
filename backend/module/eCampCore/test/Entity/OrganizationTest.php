<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\CampType;
use eCamp\Core\Entity\Organization;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class OrganizationTest extends AbstractTestCase {
    public function testOrganization() {
        $organization = new Organization();
        $organization->setName('OrganizationName');

        $this->assertEquals('OrganizationName', $organization->getName());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $organization->getCampTypes());
    }

    public function testCampTypes() {
        $campType = new CampType();
        $organization = new Organization();

        $this->assertEquals(0, $organization->getCampTypes()->count());
        $organization->addCampType($campType);
        $this->assertContains($campType, $organization->getCampTypes());
        $organization->removeCampType($campType);
        $this->assertEquals(0, $organization->getCampTypes()->count());
    }
}
