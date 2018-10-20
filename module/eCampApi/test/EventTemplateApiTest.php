<?php

namespace eCamp\ApiTest;

use eCamp\Core\Entity\Event;
use eCamp\Core\Entity\EventCategory;
use eCamp\Core\Entity\EventTemplate;

class EventTemplateApiTest extends AbstractApiTestCase {
    function setUp() {
        parent::setUp();
        $this->createDummyData();
    }

    public function testFetchAllEventTemplates() {
        /** @var EventTemplate $eventTemplate */
        $eventTemplate = $this->getRandomEntity(EventTemplate::class);

        $this->dispatchGet("/api/event_template");
        $json = $this->getResponseJson();

        $items = $json->_embedded->items;
        $eventTemplateIds = array_map(function($i) {
            return $i->id;
        }, $items);

        $this->assertContains($eventTemplate->getId(), $eventTemplateIds);
    }

    public function testFetchEventTemplateByEventTypeAndMedium() {
        /** @var EventTemplate $eventTemplate */
        $eventTemplate = $this->getRandomEntity(EventTemplate::class);

        $url = "/api/event_template?event_type_id=%s&medium=%s";
        $url = sprintf($url, $eventTemplate->getEventType()->getId(), $eventTemplate->getMedium()->getName());
        $this->dispatchGet($url);
        $json = $this->getResponseJson();

        $this->assertEquals(1, $json->total_items);
        $et = $json->_embedded->items[0];

        $this->assertEquals($eventTemplate->getId(), $et->id);
    }


    public function testFetchEventTemplateByEventCategoryAndMedium() {
        /** @var EventCategory $eventCategory */
        $eventCategory = $this->getRandomEntity(EventCategory::class);
        $eventTemplate = $eventCategory->getEventType()->getEventTemplates()->first();

        $camp = $eventCategory->getCamp();
        $user = $camp->getCreator();

        $this->login($user->getId());

        $url = "/api/event_template?event_category_id=%s&medium=%s";
        $url = sprintf($url, $eventCategory->getId(), $eventTemplate->getMedium()->getName());
        $this->dispatchGet($url);
        $json = $this->getResponseJson();

        $this->assertEquals(1, $json->total_items);
        $et = $json->_embedded->items[0];

        $this->assertEquals($eventTemplate->getId(), $et->id);
    }


    public function testFetchEventTemplateByEventAndMedium() {
        /** @var Event $event */
        $event = $this->getRandomEntity(Event::class);
        $eventTemplate = $event->getEventType()->getEventTemplates()->first();

        $camp = $event->getCamp();
        $user = $camp->getCreator();

        $this->login($user->getId());

        $url = "/api/event_template?event_id=%s&medium=%s";
        $url = sprintf($url, $event->getId(), $eventTemplate->getMedium()->getName());
        $this->dispatchGet($url);
        $json = $this->getResponseJson();

        $this->assertEquals(1, $json->total_items);
        $et = $json->_embedded->items[0];

        $this->assertEquals($eventTemplate->getId(), $et->id);
    }
}