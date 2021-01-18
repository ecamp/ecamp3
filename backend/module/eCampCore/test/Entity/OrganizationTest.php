<?php

namespace eCamp\CoreTest\Entity;

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
    }
}
