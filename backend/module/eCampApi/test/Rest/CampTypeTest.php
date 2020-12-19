<?php

namespace eCamp\ApiTest\Rest;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\CampType;
use eCamp\Core\Entity\User;
use eCamp\CoreTest\Data\CampTypeTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class CampTypeTest extends AbstractApiControllerTestCase {
    /** @var CampType */
    protected $campType;

    /** @var User */
    protected $user;

    private $apiEndpoint = '/api/camp-types';

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $campTypeLoader = new CampTypeTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($campTypeLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->campType = $campTypeLoader->getReference(CampTypeTestData::$TYPE1);

        $this->authenticateUser($this->user);
    }

    public function testFetch() {
        $this->dispatch("{$this->apiEndpoint}/{$this->campType->getId()}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "id": "{$this->campType->getId()}",
                "name": "CampType1",
                "isJS": false,
                "isCourse": false
            }
JSON;

        $expectedLinks = <<<JSON
            {
                "self": {
                    "href": "http://{$this->host}{$this->apiEndpoint}/{$this->campType->getId()}"
                }
            }
JSON;
        $expectedEmbeddedObjects = ['organization', 'activityTypes'];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testFetchAll() {
        $this->dispatch("{$this->apiEndpoint}?page_size=10", 'GET');

        $this->assertResponseStatusCode(200);

        $this->assertEquals(1, $this->getResponseContent()->total_items);
        $this->assertEquals(10, $this->getResponseContent()->page_size);
        $this->assertEquals("http://{$this->host}{$this->apiEndpoint}?page_size=10&page=1", $this->getResponseContent()->_links->self->href);
        $this->assertEquals($this->campType->getId(), $this->getResponseContent()->_embedded->items[0]->id);
    }

    public function testCreateForbidden() {
        $this->dispatch("{$this->apiEndpoint}", 'POST');
        $this->assertResponseStatusCode(405);
    }

    public function testPatchForbidden() {
        $this->dispatch("{$this->apiEndpoint}/{$this->campType->getId()}", 'PATCH');
        $this->assertResponseStatusCode(405);
    }

    public function testUpdateForbidden() {
        $this->dispatch("{$this->apiEndpoint}/{$this->campType->getId()}", 'PUT');
        $this->assertResponseStatusCode(405);
    }

    public function testDeleteForbidden() {
        $this->dispatch("{$this->apiEndpoint}/{$this->campType->getId()}", 'DELETE');
        $this->assertResponseStatusCode(405);
    }
}
