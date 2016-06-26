<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\CampType;

class CampTypeTest extends \PHPUnit_Framework_TestCase
{

    public static function createCampType($isCourse, $organization, $isJS)
    {
        return new CampType('CampType.Name', $isCourse, $organization, $isJS);
    }

    public function testCampType()
    {
        $campType = self::createCampType(true, CampType::ORGANIZATION_PBS, true);

        $this->assertEquals('CampType.Name', $campType->getName());
        $this->assertEquals(true, $campType->isCourse());
        $this->assertEquals(CampType::ORGANIZATION_PBS, $campType->getOrganization());
        $this->assertEquals(true, $campType->isJS());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $campType->getEventTypes());
    }

}
