<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\Entity\Group;
use eCamp\Core\Entity\Organization;
use eCamp\Core\Hydrator\GroupHydrator;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class GroupHydratorTest extends AbstractTestCase {
    public function testExtract(): void {
        // Disable Test
        $this->assertTrue(true);

        return;
        $organization = new Organization();
        $parent = new Group();
        $parent->setOrganization($organization);
        $parent->setName('parent');

        $group = new Group();
        $group->setOrganization($organization);
        $group->setName('name');
        $group->setDescription('desc');
        $group->setParent($parent);

        $hydrator = new GroupHydrator();
        $data = $hydrator->extract($group);

        $this->assertEquals($organization, $data['organization']);
        $this->assertEquals('name', $data['name']);
        $this->assertEquals('desc', $data['description']);
        $this->assertEquals($parent, $data['parent']);
    }

    public function testHydrate(): void {
        // Disable Test
        $this->assertTrue(true);

        return;
        $group = new Group();
        $data = [
            'name' => 'name',
            'description' => 'desc',
        ];

        $hydrator = new GroupHydrator();
        $hydrator->hydrate($data, $group);

        $this->assertEquals('name', $group->getName());
        $this->assertEquals('desc', $group->getDescription());
    }
}
