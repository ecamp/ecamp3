<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\CampType;

class CampTypeTest extends \PHPUnit_Framework_TestCase
{

    public function testCampType()
    {
        $campType = new CampType('CampType Name', 'CampType Type');

        $this->assertEquals('CampType Name', $campType->getName());
        $this->assertEquals('CampType Type', $campType->getType());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $campType->getEventTypes());
    }

}
