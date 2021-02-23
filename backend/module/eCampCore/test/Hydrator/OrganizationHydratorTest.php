<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\Entity\Organization;
use eCamp\Core\Hydrator\OrganizationHydrator;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class OrganizationHydratorTest extends AbstractTestCase {
    public function testExtract(): void {
        $organization = new Organization();
        $organization->setName('name');

        $hydrator = new OrganizationHydrator();
        $data = $hydrator->extract($organization);

        $this->assertEquals('name', $data['name']);
    }

    public function testHydrate(): void {
        $organization = new Organization();
        $data = [
            'name' => 'name',
        ];

        $hydrator = new OrganizationHydrator();
        $hydrator->hydrate($data, $organization);

        $this->assertEquals('name', $organization->getName());
    }
}
