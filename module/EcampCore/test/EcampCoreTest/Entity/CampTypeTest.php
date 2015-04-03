<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\CampType;

class CampTypeTest extends \PHPUnit_Framework_TestCase
{

    public static function createCampType()
    {
        return new CampType('CampType.Name', 'CampType.Type');
    }

    public function testCampType()
    {
        $campType = self::createCampType();

        $this->assertEquals('CampType.Name', $campType->getName());
        $this->assertEquals('CampType.Type', $campType->getType());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $campType->getEventTypes());
    }

}
