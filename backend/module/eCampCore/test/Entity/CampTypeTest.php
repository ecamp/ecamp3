<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\CampType;
use eCamp\Core\Entity\EventType;
use eCamp\Core\Entity\Organization;
use eCamp\LibTest\PHPUnit\AbstractTestCase;
use Zend\Json\Json;

/**
 * @internal
 */
class CampTypeTest extends AbstractTestCase {
    public function testCampType() {
        $organization = new Organization();
        $organization->setName('PBS');

        $config = Json::encode(['test' => 3]);

        $campType = new CampType();
        $campType->setName('CampType.Name');
        $campType->setOrganization($organization);
        $campType->setIsJS(true);
        $campType->setIsCourse(true);
        $campType->setJsonConfig($config);

        $this->assertEquals('CampType.Name', $campType->getName());
        $this->assertEquals(true, $campType->getIsCourse());
        $this->assertEquals($organization, $campType->getOrganization());
        $this->assertEquals(true, $campType->getIsJS());
        $this->assertEquals($config, $campType->getJsonConfig());
        $this->assertEquals(3, $campType->getConfig('test'));

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $campType->getEventTypes());
    }

    public function testEventType() {
        $campType = new CampType();
        $eventType = new EventType();

        $this->assertEquals(0, $campType->getEventTypes()->count());
        $campType->addEventType($eventType);
        $this->assertContains($eventType, $campType->getEventTypes());
        $campType->removeEventType($eventType);
        $this->assertEquals(0, $campType->getEventTypes()->count());
    }
}
