<?php

namespace eCamp\ApiTest\Rest;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\Period;
use eCamp\Core\Entity\User;
use eCamp\CoreTest\Data\PeriodTestData;
use eCamp\CoreTest\Data\ScheduleEntryTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class PeriodTest extends AbstractApiControllerTestCase {
    /** @var Period */
    protected $period;

    /** @var User */
    protected $user;

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $periodLoader = new PeriodTestData();
        $scheduleEntryTestData = new ScheduleEntryTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($periodLoader);
        $loader->addFixture($scheduleEntryTestData);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->period = $periodLoader->getReference(PeriodTestData::$PERIOD1);

        $this->authenticateUser($this->user);
    }

    public function testFetch(): void {
        $this->dispatch("/api/periods/{$this->period->getId()}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "id": "{$this->period->getId()}",
                "description": "Period1",
                "start": "2000-01-01",
                "end": "2000-01-13"
            }
JSON;

        $expectedLinks = <<<JSON
            {
                "self": {
                    "href": "http://{$this->host}/api/periods/{$this->period->getId()}"
                }
            }
JSON;
        $expectedEmbeddedObjects = ['camp', 'days', 'scheduleEntries'];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);

        $this->assertCount(13, $this->getResponseContent()->_embedded->days);
    }

    public function testFetchAll(): void {
        $campId = $this->period->getCamp()->getId();
        $this->dispatch("/api/periods?page_size=10&campId={$campId}", 'GET');

        $this->assertResponseStatusCode(200);

        $this->assertEquals(1, $this->getResponseContent()->total_items);
        $this->assertEquals(10, $this->getResponseContent()->page_size);
        $this->assertEquals("http://{$this->host}/api/periods?page_size=10&campId={$campId}&page=1", $this->getResponseContent()->_links->self->href);
        $this->assertEquals($this->period->getId(), $this->getResponseContent()->_embedded->items[0]->id);
    }

    public function testCreateWithoutStartAndEnd(): void {
        $this->setRequestContent([
            'description' => '', ]);

        $this->dispatch('/api/periods', 'POST');

        $this->assertResponseStatusCode(422);
        $this->assertObjectHasAttribute('isEmpty', $this->getResponseContent()->validation_messages->start);
        $this->assertObjectHasAttribute('isEmpty', $this->getResponseContent()->validation_messages->end);
    }

    public function testCreateWithoutCamp(): void {
        $this->setRequestContent([
            'description' => '',
            'start' => '2000-07-05',
            'end' => '2000-07-08',
            'campId' => 'xxx', ]);

        $this->dispatch('/api/periods', 'POST');

        $this->assertResponseStatusCode(422);
        $this->assertObjectHasAttribute('notFound', $this->getResponseContent()->validation_messages->campId);
    }

    public function testCreateSuccess(): void {
        $this->setRequestContent([
            'description' => '',
            'start' => '2000-07-05',
            'end' => '2000-07-08',
            'campId' => $this->period->getCamp()->getId(), ]);

        $this->dispatch('/api/periods', 'POST');

        $this->assertResponseStatusCode(201);
        $this->assertEquals('2000-07-05', $this->getResponseContent()->start);
    }

    public function testUpdateSuccess(): void {
        $this->setRequestContent([
            'start' => '1999-12-15', ]);

        $this->dispatch("/api/periods/{$this->period->getId()}", 'PATCH');

        $this->assertResponseStatusCode(200);

        $this->assertEquals('1999-12-15', $this->getResponseContent()->start);
        $this->assertEquals('2000-01-13', $this->getResponseContent()->end);
    }

    public function testUpdateWithMoveSucceedsWhenMoovingToPastWithMoveScheduleEntries(): void {
        $this->setRequestContent([
            'start' => '1999-01-01',
            'end' => '1999-01-13',
            'moveScheduleEntries' => true,
        ]);

        $this->dispatch("/api/periods/{$this->period->getId()}", 'PATCH');

        $this->assertResponseStatusCode(200);
        $this->assertEquals('1999-01-01', $this->getResponseContent()->start);
        $this->assertEquals('1999-01-13', $this->getResponseContent()->end);
    }

    public function testUpdateWithMoveSucceedsWhenMoovingToFutureWithMoveScheduleEntries(): void {
        $this->setRequestContent([
            'start' => '2001-01-01',
            'end' => '2001-01-13',
            'moveScheduleEntries' => true,
        ]);

        $this->dispatch("/api/periods/{$this->period->getId()}", 'PATCH');

        $this->assertEquals('2001-01-01', $this->getResponseContent()->start);
        $this->assertEquals('2001-01-13', $this->getResponseContent()->end);
    }

    public function testUpdateWithoutMoveFailsWhenMoovingToFarInPast(): void {
        $this->setRequestContent([
            'start' => '1999-01-01',
            'end' => '1999-01-13',
        ]);

        $this->dispatch("/api/periods/{$this->period->getId()}", 'PATCH');

        $this->assertResponseStatusCode(422);
    }

    public function testUpdateWithoutMoveFailsWhenMoovingToFarInFuture(): void {
        $this->setRequestContent([
            'start' => '2001-01-01',
            'end' => '2001-01-13',
        ]);

        $this->dispatch("/api/periods/{$this->period->getId()}", 'PATCH');

        $this->assertResponseStatusCode(422);
    }

    public function testUpdateWithoutMoveFailsWhenShorteningThePeriod(): void {
        $this->setRequestContent([
            'start' => '2001-01-01',
            'end' => '2001-01-02',
        ]);

        $this->dispatch("/api/periods/{$this->period->getId()}", 'PATCH');

        $this->assertResponseStatusCode(422);
    }

    public function testUpdateWithMoveFailsWhenShorteningThePeriod(): void {
        $this->setRequestContent([
            'start' => '2001-01-01',
            'end' => '2001-01-02',
            'moveScheduleEntries' => true,
        ]);

        $this->dispatch("/api/periods/{$this->period->getId()}", 'PATCH');

        $this->assertResponseStatusCode(422);
    }

    public function testDelete(): void {
        $this->dispatch("/api/periods/{$this->period->getId()}", 'DELETE');

        $this->assertResponseStatusCode(204);

        $result = $this->getEntityManager()->find(Period::class, $this->period->getId());
        $this->assertNull($result);
    }
}
