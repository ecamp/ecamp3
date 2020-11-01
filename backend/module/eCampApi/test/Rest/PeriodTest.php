<?php

namespace eCamp\ApiTest\Rest;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\Period;
use eCamp\Core\Entity\User;
use eCamp\CoreTest\Data\PeriodTestData;
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

    public function setUp() {
        parent::setUp();

        $userLoader = new UserTestData();
        $periodLoader = new PeriodTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($periodLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->period = $periodLoader->getReference(PeriodTestData::$PERIOD1);

        $this->authenticateUser($this->user);
    }

    public function testFetch() {
        $this->dispatch("/api/periods/{$this->period->getId()}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "id": "{$this->period->getId()}",
                "description": "Period1",
                "start": "2000-01-01",
                "end": "2000-01-03"
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
    }

    public function testFetchAll() {
        $campId = $this->period->getCamp()->getId();
        $this->dispatch("/api/periods?page_size=10&campId={$campId}", 'GET');

        $this->assertResponseStatusCode(200);

        $this->assertEquals(1, $this->getResponseContent()->total_items);
        $this->assertEquals(10, $this->getResponseContent()->page_size);
        $this->assertEquals("http://{$this->host}/api/periods?page_size=10&campId={$campId}&page=1", $this->getResponseContent()->_links->self->href);
        $this->assertEquals($this->period->getId(), $this->getResponseContent()->_embedded->items[0]->id);
    }

    public function testCreateWithoutStartAndEnd() {
        $this->setRequestContent([
            'description' => '', ]);

        $this->dispatch('/api/periods', 'POST');

        $this->assertResponseStatusCode(422);
        $this->assertObjectHasAttribute('isEmpty', $this->getResponseContent()->validation_messages->start);
        $this->assertObjectHasAttribute('isEmpty', $this->getResponseContent()->validation_messages->end);
    }

    public function testCreateWithoutCamp() {
        $this->setRequestContent([
            'description' => '',
            'start' => '2000-07-05',
            'end' => '2000-07-08',
            'campId' => 'xxx', ]);

        $this->dispatch('/api/periods', 'POST');

        $this->assertResponseStatusCode(422);
        $this->assertObjectHasAttribute('notFound', $this->getResponseContent()->validation_messages->campId);
    }

    public function testCreateSuccess() {
        $this->setRequestContent([
            'description' => '',
            'start' => '2000-07-05',
            'end' => '2000-07-08',
            'campId' => $this->period->getCamp()->getId(), ]);

        $this->dispatch('/api/periods', 'POST');

        $this->assertResponseStatusCode(201);
        $this->assertEquals('2000-07-05', $this->getResponseContent()->start);
    }

    public function testUpdateSuccess() {
        $this->setRequestContent([
            'start' => '1999-12-15', ]);

        $this->dispatch("/api/periods/{$this->period->getId()}", 'PATCH');

        $this->assertResponseStatusCode(200);

        $this->assertEquals('1999-12-15', $this->getResponseContent()->start);
        $this->assertEquals('2000-01-03', $this->getResponseContent()->end);
    }

    public function testDelete() {
        $this->dispatch("/api/periods/{$this->period->getId()}", 'DELETE');

        $this->assertResponseStatusCode(204);

        $result = $this->getEntityManager()->find(Period::class, $this->period->getId());
        $this->assertNull($result);
    }
}
