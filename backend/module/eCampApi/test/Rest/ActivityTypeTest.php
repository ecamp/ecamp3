<?php

namespace eCamp\ApiTest\Rest;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\ActivityType;
use eCamp\Core\Entity\CampType;
use eCamp\Core\Entity\User;
use eCamp\CoreTest\Data\ActivityTypeTestData;
use eCamp\CoreTest\Data\CampTypeTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class ActivityTypeTest extends AbstractApiControllerTestCase {
    /** @var ActivityType */
    protected $activityType;

    /** @var CampType */
    protected $campType;

    /** @var User */
    protected $user;

    private $apiEndpoint = '/api/activity-types';

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $activityTypeLoader = new ActivityTypeTestData();
        $campTypeLoader = new CampTypeTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($activityTypeLoader);
        $loader->addFixture($campTypeLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->activityType = $activityTypeLoader->getReference(ActivityTypeTestData::$TYPE1);
        $this->campType = $activityTypeLoader->getReference(CampTypeTestData::$TYPE1);

        $this->authenticateUser($this->user);
    }

    public function testFetch() {
        $this->dispatch("{$this->apiEndpoint}/{$this->activityType->getId()}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "id": "{$this->activityType->getId()}",
                "name": "ActivityType1",
                "template": "General",
                "defaultColor": "#FF00FF",
                "defaultNumberingStyle": "i"
            }
JSON;

        $expectedLinks = <<<JSON
            {
                "self": {
                    "href": "http://{$this->host}{$this->apiEndpoint}/{$this->activityType->getId()}"
                }
            }
JSON;
        $expectedEmbeddedObjects = ['activityTypeContentTypes'];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testFetchAll() {
        $campTypeId = $this->campType->getId();
        $this->dispatch("{$this->apiEndpoint}?page_size=10&campTypeId={$campTypeId}", 'GET');

        $this->assertResponseStatusCode(200);

        $this->assertEquals(1, $this->getResponseContent()->total_items);
        $this->assertEquals(10, $this->getResponseContent()->page_size);
        $this->assertEquals("http://{$this->host}{$this->apiEndpoint}?page_size=10&campTypeId={$campTypeId}&page=1", $this->getResponseContent()->_links->self->href);
        $this->assertEquals($this->activityType->getId(), $this->getResponseContent()->_embedded->items[0]->id);
    }

    public function testCreateForbidden() {
        $this->dispatch("{$this->apiEndpoint}", 'POST');
        $this->assertResponseStatusCode(405);
    }

    public function testPatchForbidden() {
        $this->dispatch("{$this->apiEndpoint}/{$this->activityType->getId()}", 'PATCH');
        $this->assertResponseStatusCode(405);
    }

    public function testUpdateForbidden() {
        $this->dispatch("{$this->apiEndpoint}/{$this->activityType->getId()}", 'PUT');
        $this->assertResponseStatusCode(405);
    }

    public function testDeleteForbidden() {
        $this->dispatch("{$this->apiEndpoint}/{$this->activityType->getId()}", 'DELETE');
        $this->assertResponseStatusCode(405);
    }
}
