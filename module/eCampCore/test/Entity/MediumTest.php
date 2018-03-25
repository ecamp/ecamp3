<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\Medium;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

class MediumTest extends AbstractTestCase
{

    public function testMedium() {
        $medium = new Medium();
        $medium->setName('TestMedium');
        $medium->setDefault(true);

        $this->assertEquals('TestMedium', $medium->getName());
        $this->assertTrue($medium->isDefault());
    }

}
