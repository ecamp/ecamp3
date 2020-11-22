<?php

namespace eCamp\ApiTest\Rest;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\Day;
use eCamp\Core\Entity\User;
use eCamp\CoreTest\Data\PeriodTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class DayTest extends AbstractApiControllerTestCase {
    /** @var Day */
    protected $day;

    /** @var User */
    protected $user;

    private $apiEndpoint = '/api/days';

    public function setUp() {
        parent::setUp();

        $userLoader = new UserTestData();
        $periodLoader = new PeriodTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($periodLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->day = $periodLoader->getReference(PeriodTestData::$DAY1);

        $this->authenticateUser($this->user);
    }

    public function testFetch() {
        $this->dispatch("{$this->apiEndpoint}/{$this->day->getId()}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "id": "{$this->day->getId()}",
                "dayOffset": 0,
                "number": 1
            }
JSON;

        $expectedLinks = <<<JSON
            {
                "self": {
                    "href": "http://{$this->host}{$this->apiEndpoint}/{$this->day->getId()}"
                }
            }
JSON;
        $expectedEmbeddedObjects = ['period', 'scheduleEntries'];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testFetchAll() {
        $periodId = $this->day->getPeriod()->getId();
        $campId = $this->day->getCamp()->getId();
        $this->dispatch("{$this->apiEndpoint}?periodId={$periodId}&campId={$campId}", 'GET');

        $this->assertResponseStatusCode(200);

        $this->assertEquals(13, $this->getResponseContent()->total_items);
        $this->assertEquals(-1, $this->getResponseContent()->page_size);
        $this->assertEquals(1, $this->getResponseContent()->page_count);
        $this->assertEquals("http://{$this->host}{$this->apiEndpoint}?periodId={$periodId}&campId={$campId}&page=1", $this->getResponseContent()->_links->self->href);
        $this->assertEquals($this->day->getId(), $this->getResponseContent()->_embedded->items[0]->id);
    }

    public function testFetchAllPaged() {
        $periodId = $this->day->getPeriod()->getId();
        $campId = $this->day->getCamp()->getId();
        $this->dispatch("{$this->apiEndpoint}?page_size=10&periodId={$periodId}&campId={$campId}", 'GET');

        $this->assertResponseStatusCode(200);

        $this->assertEquals(13, $this->getResponseContent()->total_items);
        $this->assertEquals(10, $this->getResponseContent()->page_size);
        $this->assertEquals(2, $this->getResponseContent()->page_count);
        $this->assertEquals("http://{$this->host}{$this->apiEndpoint}?page_size=10&periodId={$periodId}&campId={$campId}&page=1", $this->getResponseContent()->_links->self->href);
        $this->assertEquals($this->day->getId(), $this->getResponseContent()->_embedded->items[0]->id);
    }

    public function testCreate() {
        $this->setRequestContent([]);

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        // Single days should not be created via API directly (only by changing the parent Period)
        $this->assertResponseStatusCode(405); //405 = Method not allowed
    }

    public function testUpdateSuccess() {
        $this->setRequestContent([
            'dayOffset' => 15, ]);

        $this->dispatch("{$this->apiEndpoint}/{$this->day->getId()}", 'PATCH');

        // PATCH allowed in principal, but cannot change dayOffset
        // no other properties implemented so far that could be changed
        $this->assertResponseStatusCode(400);
    }

    public function testDelete() {
        $this->dispatch("{$this->apiEndpoint}/{$this->day->getId()}", 'DELETE');

        // Single days should not be deleted via API directly (only by changing the parent Period)
        $this->assertResponseStatusCode(405); //405 = Method not allowed
    }
}
