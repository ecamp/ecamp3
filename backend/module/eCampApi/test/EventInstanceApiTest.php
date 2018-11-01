<?php

namespace eCamp\ApiTest;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Day;
use eCamp\Core\Entity\Event;
use eCamp\Core\Entity\EventCategory;
use eCamp\Core\Entity\EventInstance;
use eCamp\Core\Entity\Period;
use eCamp\Core\EntityService\EventInstanceService;
use eCamp\Core\EntityService\EventService;
use Zend\Http\Response;

class EventInstanceApiTest extends AbstractApiTestCase {
    function setUp() {
        parent::setUp();
        $this->createDummyData();
    }

    public function testGetEventInstance() {
        /** @var EventInstance $eventInstance */
        $eventInstance = $this->getRandomEntity(EventInstance::class);
        $eventInstanceId = $eventInstance->getId();
        $camp = $eventInstance->getCamp();
        $this->login($camp->getCreator()->getId());

        $this->dispatchGet(
            "/api/event_instance/" . $eventInstanceId
        );
        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $json = $this->getResponseJson();

        $this->assertEquals($eventInstanceId, $json->id);
    }

    public function testGetEventInstancesByPeriod() {
        /** @var Period $period */
        $period = $this->getRandomEntity(Period::class);
        $periodId = $period->getId();
        $camp = $period->getCamp();
        $this->login($camp->getCreator()->getId());

        $this->dispatchGet(
            "/api/event_instance?period_id=" . $periodId
        );
        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $json = $this->getResponseJson();

        $this->assertCount($period->getEventInstances()->count(), $json->_embedded->items);
    }

    public function testGetEventInstancesByDay() {
        /** @var Day $day */
        $day = $this->getRandomEntity(Day::class);
        $dayId = $day->getId();
        $camp = $day->getCamp();
        $this->login($camp->getCreator()->getId());

        $this->dispatchGet(
            "/api/event_instance?day_id=" . $dayId
        );
        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $json = $this->getResponseJson();

        $this->assertCount($day->getEventInstances()->count(), $json->_embedded->items);
    }

    public function testCreateEvent() {
        /** @var Period $period */
        $period = $this->getRandomEntity(Period::class);
        $periodId = $period->getId();
        $camp = $period->getCamp();
        $this->login($camp->getCreator()->getId());

        /** @var Event $event */
        $events = $camp->getEvents();
        $event = $events->first();
        $eventId = $event->getId();

        $this->dispatchPost(
            "/api/event_instance",
            [
                'period_id' => $periodId,
                'event_id' => $eventId,
                'start' => 600,
                'length' => 120,
                'left' => 0,
                'width' => 1
            ]
        );
        $json = $this->getResponseJson();

        /** @var EventInstanceService $eventInstanceService */
        $eventInstanceService = $this->getService(EventInstanceService::class);
        /** @var EventInstance $eventInstance */
        $eventInstance = $eventInstanceService->fetch($json->id);

        $this->assertEquals(600, $eventInstance->getStart());
    }

    public function testUpdateEventInstance() {
        /** @var EventInstance $eventInstance */
        $eventInstance = $this->getRandomEntity(EventInstance::class);
        $camp = $eventInstance->getCamp();
        $this->login($camp->getCreator()->getId());

        $start = $eventInstance->getStart();
        if ($start > 0) {
            $start = $start - 1;
        } else {
            $start = $start + 1;
        }

        $this->dispatchPatch(
            "/api/event_instance/" . $eventInstance->getId(),
            [ 'start' => $start ]
        );
        $json = $this->getResponseJson();

        /** @var EventInstanceService $eventInstanceService */
        $eventInstanceService = $this->getService(EventInstanceService::class);
        /** @var EventInstance $eventInstance */
        $eventInstance = $eventInstanceService->fetch($json->id);

        $this->assertEquals($start, $eventInstance->getStart());
    }

    public function testDeleteEvent() {
        /** @var EventInstance $eventInstance */
        $eventInstance = $this->getRandomEntity(EventInstance::class);
        $camp = $eventInstance->getCamp();
        $this->login($camp->getCreator()->getId());

        $eventInstanceId = $eventInstance->getId();

        $this->dispatchDelete("/api/event_instance/" . $eventInstanceId);

        /** @var EventInstanceService $eventInstanceService */
        $eventInstanceService = $this->getService(EventInstanceService::class);
        /** @var EventInstance $eventInstance */
        $eventInstance = $eventInstanceService->fetch($eventInstanceId);

        $this->assertNull($eventInstance);
    }

    public function testCanNotDeleteEvent1() {
        $this->logout();

        /** @var EventInstance $eventInstance */
        $eventInstance = $this->getRandomEntity(EventInstance::class);

        $eventInstanceId = $eventInstance->getId();
        $this->dispatchDelete("/api/event_instance/" . $eventInstanceId);

        // Event not found
        $this->assertResponseStatusCode(Response::STATUS_CODE_404);
    }
}