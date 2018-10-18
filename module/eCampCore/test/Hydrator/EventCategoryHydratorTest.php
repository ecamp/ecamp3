<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\EventCategory;
use eCamp\Core\Entity\EventType;
use eCamp\Core\Hydrator\EventCategoryHydrator;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

class EventCategoryHydratorTest extends AbstractTestCase {
    public function testExtract() {
        $eventType = new EventType();
        $camp = new Camp();
        $eventCategory = new EventCategory();
        $eventCategory->setEventType($eventType);
        $eventCategory->setCamp($camp);
        $eventCategory->setShort('sh');
        $eventCategory->setName('name');
        $eventCategory->setColor('#ff0000');
        $eventCategory->setNumberingStyle('i');

        $hydrator = new EventCategoryHydrator();
        $data = $hydrator->extract($eventCategory);

        $this->assertEquals($eventType, $data['event_type']);
        $this->assertEquals($camp, $data['camp']);
        $this->assertEquals('sh', $data['short']);
        $this->assertEquals('name', $data['name']);
        $this->assertEquals('#ff0000', $data['color']);
        $this->assertEquals('i', $data['numbering_style']);
    }

    public function testHydrate() {
        $eventType = new EventType();
        $camp = new Camp();

        $eventCategory = new EventCategory();
        $data = [
            'short' => 'sh',
            'name' => 'name',
            'color' => '#00ff00',
            'numbering_style' => 'a'
        ];

        $hydrator = new EventCategoryHydrator();
        $hydrator->hydrate($data, $eventCategory);

        $this->assertEquals('sh', $eventCategory->getShort());
        $this->assertEquals('name', $eventCategory->getName());
        $this->assertEquals('#00ff00', $eventCategory->getColor());
        $this->assertEquals('a', $eventCategory->getNumberingStyle());
    }
}
