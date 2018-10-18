<?php

namespace eCamp\ApiTest;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Event;
use eCamp\Core\Entity\EventCategory;
use eCamp\Core\EntityService\EventService;
use Zend\Http\Response;

class EventApiTest extends AbstractApiTestCase {
    function setUp() {
        parent::setUp();
        $this->createDummyData();
    }

    public function testGetEvents() {
        /** @var Camp $camp */
        $camp = $this->getRandomEntity(Camp::class);
        $campId = $camp->getId();
        $this->login($camp->getCreator()->getId());

        $this->dispatchGet(
            "/api/event?camp_id=" . $campId
        );
        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $json = $this->getResponseJson();

        $this->assertCount($camp->getEvents()->count(), $json->_embedded->items);
    }

    public function testCreateEvent() {
        /** @var Camp $camp */
        $camp = $this->getRandomEntity(Camp::class);
        $campId = $camp->getId();
        $this->login($camp->getCreator()->getId());

        /** @var EventCategory $category */
        $categories = $camp->getEventCategories();
        $category = $categories->first();
        $categoryId = $category->getId();

        $this->dispatchPost(
            "/api/event",
            [
                'camp_id' => $campId,
                'event_category_id' => $categoryId,
                'title' => 'test'
            ]
        );
        $json = $this->getResponseJson();

        /** @var EventService $eventService */
        $eventService = $this->getService(EventService::class);
        /** @var Event $event */
        $event = $eventService->fetch($json->id);

        $this->assertEquals('test', $event->getTitle());
    }

    public function testUpdateEvent() {
        /** @var Event $event */
        $event = $this->getRandomEntity(Event::class);
        $camp = $event->getCamp();
        $this->login($camp->getCreator()->getId());

        $title = $event->getTitle() . ' :: new title';

        $this->dispatchPatch(
            "/api/event/" . $event->getId(),
            [ 'title' => $title ]
        );
        $json = $this->getResponseJson();

        /** @var EventService $eventService */
        $eventService = $this->getService(EventService::class);
        /** @var Event $event */
        $event = $eventService->fetch($json->id);

        $this->assertEquals($title, $event->getTitle());
    }

    public function testDeleteEvent() {
        /** @var Event $event */
        $event = $this->getRandomEntity(Event::class);
        $camp = $event->getCamp();
        $this->login($camp->getCreator()->getId());

        $eventId = $event->getId();

        $this->dispatchDelete("/api/event/" . $eventId);

        /** @var EventService $eventService */
        $eventService = $this->getService(EventService::class);
        /** @var Event $event */
        $event = $eventService->fetch($eventId);

        $this->assertNull($event);
    }


    public function testCanNotDeleteEvent1() {
        $this->logout();

        /** @var Event $event */
        $event = $this->getRandomEntity(Event::class);

        $eventId = $event->getId();
        $this->dispatchDelete("/api/event/" . $eventId);

        // Event not found
        $this->assertResponseStatusCode(Response::STATUS_CODE_404);
    }
}