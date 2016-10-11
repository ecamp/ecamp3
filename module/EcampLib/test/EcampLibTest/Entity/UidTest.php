<?php

namespace EcampLibTest\Entity;

use EcampLib\Entity\BaseEntity;
use EcampLib\Entity\UId;

class UidTest extends \PHPUnit_Framework_TestCase
{
    public function testUid()
    {
        $uid = new UId('Test');

        $this->assertEquals('Test', $uid->getClass());
    }
}
