<?php

namespace eCamp\ApiTest\Rest;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\ContentTypeConfigTemplate;
use eCamp\Core\Entity\User;
use eCamp\CoreTest\Data\ActivityCategoryTemplateTestData;
use eCamp\CoreTest\Data\ContentTypeConfigTemplateTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class ContentTypeConfigTemplateTest extends AbstractApiControllerTestCase {
    /** @var ContentTypeConfigTemplate */
    protected $contentTypeConfigTemplate;

    /** @var ActivityCategoryTemplate */
    protected $activityCategoryTemplate;

    /** @var User */
    protected $user;

    private $apiEndpoint = '/api/content-type-config-templates';

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $activityCategoryTemplateLoader = new ActivityCategoryTemplateTestData();
        $contentTypeConfigTemplateLoader = new ContentTypeConfigTemplateTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($activityCategoryTemplateLoader);
        $loader->addFixture($contentTypeConfigTemplateLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->activityCategoryTemplate = $activityCategoryTemplateLoader->getReference(ActivityCategoryTemplateTestData::$TEMPLATE1);
        $this->contentTypeConfigTemplate = $contentTypeConfigTemplateLoader->getReference(ContentTypeConfigTemplateTestData::$TEMPLATE1);

        $this->authenticateUser($this->user);
    }

    public function testFetch() {
        $this->dispatch("{$this->apiEndpoint}/{$this->contentTypeConfigTemplate->getId()}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "id": "{$this->contentTypeConfigTemplate->getId()}",
                "required": true,
                "multiple": true
            }
JSON;

        $expectedLinks = <<<JSON
            {
                "self": {
                    "href": "http://{$this->host}{$this->apiEndpoint}/{$this->contentTypeConfigTemplate->getId()}"
                }
            }
JSON;
        $expectedEmbeddedObjects = ['contentType'];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testFetchAll() {
        $activityCategoryTemplateId = $this->activityCategoryTemplate->getId();
        $this->dispatch("{$this->apiEndpoint}?page_size=10&activityCategoryTemplateId={$activityCategoryTemplateId}", 'GET');

        $this->assertResponseStatusCode(200);

        $this->assertEquals(1, $this->getResponseContent()->total_items);
        $this->assertEquals(10, $this->getResponseContent()->page_size);
        $this->assertEquals("http://{$this->host}{$this->apiEndpoint}?page_size=10&activityCategoryTemplateId={$activityCategoryTemplateId}&page=1", $this->getResponseContent()->_links->self->href);
        $this->assertEquals($this->contentTypeConfigTemplate->getId(), $this->getResponseContent()->_embedded->items[0]->id);
    }

    public function testCreateForbidden() {
        $this->dispatch("{$this->apiEndpoint}", 'POST');
        $this->assertResponseStatusCode(405);
    }

    public function testPatchForbidden() {
        $this->dispatch("{$this->apiEndpoint}/{$this->contentTypeConfigTemplate->getId()}", 'PATCH');
        $this->assertResponseStatusCode(405);
    }

    public function testUpdateForbidden() {
        $this->dispatch("{$this->apiEndpoint}/{$this->contentTypeConfigTemplate->getId()}", 'PUT');
        $this->assertResponseStatusCode(405);
    }

    public function testDeleteForbidden() {
        $this->dispatch("{$this->apiEndpoint}/{$this->contentTypeConfigTemplate->getId()}", 'DELETE');
        $this->assertResponseStatusCode(405);
    }
}
