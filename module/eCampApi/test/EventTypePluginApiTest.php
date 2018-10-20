<?php

namespace eCamp\ApiTest;

use eCamp\Core\Entity\EventTypePlugin;

class EventTypePluginApiTest extends AbstractApiTestCase {
    function setUp() {
        parent::setUp();
        $this->createDummyData();
    }

    public function testFetchAllEventTypePlugins() {
        /** @var EventTypePlugin $eventTypePlugin */
        $eventTypePlugin = $this->getRandomEntity(EventTypePlugin::class);

        $this->dispatchGet("/api/event_type_plugin");
        $json = $this->getResponseJson();

        $items = $json->_embedded->items;
        $eventTypePluginIds = array_map(function($i) {
            return $i->id;
        }, $items);

        $this->assertContains($eventTypePlugin->getId(), $eventTypePluginIds);
    }
}