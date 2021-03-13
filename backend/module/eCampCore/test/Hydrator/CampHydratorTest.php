<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Hydrator\CampHydrator;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class CampHydratorTest extends AbstractTestCase {
    public function testExtract(): void {
        $camp = new Camp();
        $camp->setName('name');
        $camp->setTitle('title');
        $camp->setMotto('motto');
        $camp->setIsPrototype(true);

        $hydrator = new CampHydrator();
        $data = $hydrator->extract($camp);

        $this->assertEquals('name', $data['name']);
        $this->assertEquals('title', $data['title']);
        $this->assertEquals('motto', $data['motto']);
        $this->assertTrue($data['isPrototype']);
    }

    public function testHydrate(): void {
        $camp = new Camp();
        $data = [
            'name' => 'name',
            'title' => 'title',
            'motto' => 'motto',
            'isPrototype' => true,
        ];

        $hydrator = new CampHydrator();
        $hydrator->hydrate($data, $camp);

        // Name must not be set by hydrator
        $this->assertEquals(null, $camp->getName());
        $this->assertEquals('title', $camp->getTitle());
        $this->assertEquals('motto', $camp->getMotto());
        $this->assertTrue($camp->getIsPrototype());
    }
}
