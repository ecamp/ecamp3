<?php

namespace eCamp\ApiTest;

use eCamp\Core\Entity\EventType;

class EventTypeApiTest extends AbstractApiTestCase {
    function setUp() {
        parent::setUp();
        $this->createDummyData();
    }

    public function testFetchAllEventTypes() {
        /** @var EventType $eventType */
        $eventType = $this->getRandomEntity(EventType::class);

        $this->dispatchGet("/api/event_type");
        $json = $this->getResponseJson();

        $items = $json->_embedded->items;
        $eventTypeIds = array_map(function($i) {
            return $i->id;
        }, $items);

        $this->assertContains($eventType->getId(), $eventTypeIds);
    }
}