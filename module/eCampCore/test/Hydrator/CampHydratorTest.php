<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Hydrator\CampHydrator;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

class CampHydratorTest extends AbstractTestCase {
    public function testExtract() {
        $camp = new Camp();
        $camp->setName('name');
        $camp->setTitle('title');
        $camp->setMotto('motto');

        $hydrator = new CampHydrator();
        $data = $hydrator->extract($camp);

        $this->assertEquals('name', $data['name']);
        $this->assertEquals('title', $data['title']);
        $this->assertEquals('motto', $data['motto']);
    }

    public function testHydrate() {
        $camp = new Camp();
        $data = [
            'name' => 'name',
            'title' => 'title',
            'motto' => 'motto'
        ];

        $hydrator = new CampHydrator();
        $hydrator->hydrate($data, $camp);

        // Name must not be set by hydrator
        $this->assertEquals(null, $camp->getName());
        $this->assertEquals('title', $camp->getTitle());
        $this->assertEquals('motto', $camp->getMotto());
    }
}
