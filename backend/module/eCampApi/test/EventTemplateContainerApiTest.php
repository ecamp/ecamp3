<?php

namespace eCamp\ApiTest;

use eCamp\Core\Entity\EventTemplateContainer;

class EventTemplateContainerApiTest extends AbstractApiTestCase {
    function setUp() {
        parent::setUp();
        $this->createDummyData();
    }

    public function testFetchAllEventTemplateContainers() {
        /** @var EventTemplateContainer $eventTemplateContainer */
        $eventTemplateContainer = $this->getRandomEntity(EventTemplateContainer::class);

        $this->dispatchGet("/api/event_template_container");
        $json = $this->getResponseJson();

        $items = $json->_embedded->items;
        $eventTemplateContainerIds = array_map(function($i) {
            return $i->id;
        }, $items);

        $this->assertContains($eventTemplateContainer->getId(), $eventTemplateContainerIds);
    }
}