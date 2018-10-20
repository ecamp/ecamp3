<?php

namespace eCamp\ApiTest;

use eCamp\Core\Entity\EventTypeFactory;

class EventTypeFactoryApiTest extends AbstractApiTestCase {
    function setUp() {
        parent::setUp();
        $this->createDummyData();
    }

    public function testFetchAllEventTypeFactories() {
        /** @var EventTypeFactory $eventTypeFactory */
        $eventTypeFactory = $this->getRandomEntity(EventTypeFactory::class);

        $this->dispatchGet("/api/event_type_factory");
        $json = $this->getResponseJson();

        $items = $json->_embedded->items;
        $eventTypeFactoryIds = array_map(function($i) {
            return $i->id;
        }, $items);

        $this->assertContains($eventTypeFactory->getId(), $eventTypeFactoryIds);
    }
}