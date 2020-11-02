<?php

namespace eCamp\ApiTest\Rest;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\ScheduleEntry;
use eCamp\Core\Entity\User;
use eCamp\CoreTest\Data\ScheduleEntryTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class ScheduleEntryTest extends AbstractApiControllerTestCase {
    /** @var ScheduleEntry */
    protected $scheduleEntry;

    /** @var User */
    protected $user;

    private $apiEndpoint = '/api/schedule-entries';

    public function setUp() {
        parent::setUp();

        $userLoader = new UserTestData();
        $scheduleEntryLoader = new ScheduleEntryTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($scheduleEntryLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->scheduleEntry = $scheduleEntryLoader->getReference(ScheduleEntryTestData::$ENTRY1);

        $this->authenticateUser($this->user);
    }

    public function testFetch() {
        $this->dispatch("{$this->apiEndpoint}/{$this->scheduleEntry->getId()}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "id": "{$this->scheduleEntry->getId()}",
                "start": 600,
                "length": 120,
                "left": 0,
                "width": 1,
                "startTime": "2000-01-01 10:00:00",
                "endTime": "2000-01-01 12:00:00",
                "dayNumber": 1,
                "scheduleEntryNumber": 1,
                "number": "1.i"
            }
JSON;

        $expectedLinks = <<<JSON
            {
                "self": {
                    "href": "http://{$this->host}{$this->apiEndpoint}/{$this->scheduleEntry->getId()}"
                }
            }
JSON;
        $expectedEmbeddedObjects = ['activity', 'period', 'day'];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testFetchAll() {
        $activityId = $this->scheduleEntry->getActivity()->getId();
        $this->dispatch("{$this->apiEndpoint}?page_size=10&activityId={$activityId}", 'GET');

        $this->assertResponseStatusCode(200);

        $this->assertEquals(1, $this->getResponseContent()->total_items);
        $this->assertEquals(10, $this->getResponseContent()->page_size);
        $this->assertEquals("http://{$this->host}{$this->apiEndpoint}?page_size=10&activityId={$activityId}&page=1", $this->getResponseContent()->_links->self->href);
        $this->assertEquals($this->scheduleEntry->getId(), $this->getResponseContent()->_embedded->items[0]->id);
    }

    public function testCreateWithoutStartAndLength() {
        $this->setRequestContent([
            'start' => '', ]);

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        $this->assertResponseStatusCode(422);
        $this->assertObjectHasAttribute('isEmpty', $this->getResponseContent()->validation_messages->start);
        $this->assertObjectHasAttribute('isEmpty', $this->getResponseContent()->validation_messages->length);
    }

    public function testCreateWithoutActivity() {
        $this->setRequestContent([
            'start' => 900,
            'length' => 180,
            'periodId' => 'xxx', ]);

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        $this->assertResponseStatusCode(422);
        $this->assertObjectHasAttribute('notFound', $this->getResponseContent()->validation_messages->periodId);
    }

    public function testCreateSuccess() {
        $this->setRequestContent([
            'start' => 900,
            'length' => 180,
            'activityId' => $this->scheduleEntry->getActivity()->getId(),
            'periodId' => $this->scheduleEntry->getPeriod()->getId(), ]);

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        $this->assertResponseStatusCode(201);
        $this->assertEquals(900, $this->getResponseContent()->start);
    }

    public function testUpdateSuccess() {
        $this->setRequestContent([
            'start' => 780, ]);

        $this->dispatch("{$this->apiEndpoint}/{$this->scheduleEntry->getId()}", 'PATCH');

        $this->assertResponseStatusCode(200);

        $this->assertEquals(780, $this->getResponseContent()->start);
        $this->assertEquals('2000-01-01 13:00:00', $this->getResponseContent()->startTime);
    }

    public function testDelete() {
        $this->dispatch("{$this->apiEndpoint}/{$this->scheduleEntry->getId()}", 'DELETE');

        $this->assertResponseStatusCode(204);

        $result = $this->getEntityManager()->find(ScheduleEntry::class, $this->scheduleEntry->getId());
        $this->assertNull($result);
    }
}
