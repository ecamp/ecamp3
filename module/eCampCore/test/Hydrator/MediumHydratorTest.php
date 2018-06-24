<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\Entity\Medium;
use eCamp\Core\Hydrator\MediumHydrator;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

class MediumHydratorTest extends AbstractTestCase {
    public function testExtract() {
        $medium = new Medium();
        $medium->setName('name');
        $medium->setDefault(true);

        $hydrator = new MediumHydrator();
        $data = $hydrator->extract($medium);

        $this->assertEquals('name', $data['name']);
        $this->assertTrue($data['default']);
    }

    public function testHydrate() {
        $medium = new Medium();
        $data = [
            'default' => true,
        ];

        $hydrator = new MediumHydrator();
        $hydrator->hydrate($data, $medium);

        $this->assertTrue($medium->isDefault());
    }
}
