<?php

namespace eCamp\ApiTest\Rest;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\ContentType;
use eCamp\Core\Entity\User;
use eCamp\CoreTest\Data\ContentTypeTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class ContentTypeTest extends AbstractApiControllerTestCase {
    /** @var ContentType */
    protected $contentType;

    /** @var User */
    protected $user;

    private $apiEndpoint = '/api/content-types';

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $contentTypeLoader = new ContentTypeTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($contentTypeLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->contentType = $contentTypeLoader->getReference(ContentTypeTestData::$TYPE1);

        $this->authenticateUser($this->user);
    }

    public function testFetch() {
        $this->dispatch("{$this->apiEndpoint}/{$this->contentType->getId()}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "id": "{$this->contentType->getId()}",
                "name": "Storyboard",
                "active": true,
                "allowMultiple": true
            }
JSON;

        $expectedLinks = <<<JSON
            {
                "self": {
                    "href": "http://{$this->host}{$this->apiEndpoint}/{$this->contentType->getId()}"
                }
            }
JSON;

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, []);
    }

    public function testFetchAll() {
        $this->dispatch("{$this->apiEndpoint}?page_size=10", 'GET');

        $this->assertResponseStatusCode(200);

        $this->assertEquals(1, $this->getResponseContent()->total_items);
        $this->assertEquals(10, $this->getResponseContent()->page_size);
        $this->assertEquals("http://{$this->host}{$this->apiEndpoint}?page_size=10&page=1", $this->getResponseContent()->_links->self->href);
        $this->assertEquals($this->contentType->getId(), $this->getResponseContent()->_embedded->items[0]->id);
    }

    public function testCreateForbidden() {
        $this->dispatch("{$this->apiEndpoint}", 'POST');
        $this->assertResponseStatusCode(405);
    }

    public function testPatchForbidden() {
        $this->dispatch("{$this->apiEndpoint}/{$this->contentType->getId()}", 'PATCH');
        $this->assertResponseStatusCode(405);
    }

    public function testUpdateForbidden() {
        $this->dispatch("{$this->apiEndpoint}/{$this->contentType->getId()}", 'PUT');
        $this->assertResponseStatusCode(405);
    }

    public function testDeleteForbidden() {
        $this->dispatch("{$this->apiEndpoint}/{$this->contentType->getId()}", 'DELETE');
        $this->assertResponseStatusCode(405);
    }
}
