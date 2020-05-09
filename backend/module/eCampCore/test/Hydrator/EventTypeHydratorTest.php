<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\Entity\EventType;
use eCamp\Core\Hydrator\EventTypeHydrator;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class EventTypeHydratorTest extends AbstractTestCase {
    public function testExtract() {
        $eventType = new EventType();
        $eventType->setName('name');
        $eventType->setDefaultColor('#ff0000');
        $eventType->setDefaultNumberingStyle('i');

        $hydrator = new EventTypeHydrator();
        $data = $hydrator->extract($eventType);

        $this->assertEquals('name', $data['name']);
        $this->assertEquals('#ff0000', $data['defaultColor']);
        $this->assertEquals('i', $data['defaultNumberingStyle']);
    }

    public function testHydrate() {
        $eventType = new EventType();
        $data = [
            'name' => 'name',
            'defaultColor' => '#00ff00',
            'defaultNumberingStyle' => 'a',
        ];

        $hydrator = new EventTypeHydrator();
        $hydrator->hydrate($data, $eventType);

        $this->assertEquals('name', $eventType->getName());
        $this->assertEquals('#00ff00', $eventType->getDefaultColor());
        $this->assertEquals('a', $eventType->getDefaultNumberingStyle());
    }
}
