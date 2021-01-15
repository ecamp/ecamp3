<?php

namespace eCamp\ApiTest\Rest;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\ActivityCategoryTemplate;
use eCamp\Core\Entity\CampTemplate;
use eCamp\Core\Entity\User;
use eCamp\CoreTest\Data\ActivityCategoryTemplateTestData;
use eCamp\CoreTest\Data\CampTemplateTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class ActivityCategoryTemplateTest extends AbstractApiControllerTestCase {
    /** @var ActivityCategoryTemplate */
    protected $activityCategoryTemplate;

    /** @var CampTemplate */
    protected $campTemplate;

    /** @var User */
    protected $user;

    private $apiEndpoint = '/api/activity-category-templates';

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $campTemplateLoader = new CampTemplateTestData();
        $activityCategoryTemplateLoader = new ActivityCategoryTemplateTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($campTemplateLoader);
        $loader->addFixture($activityCategoryTemplateLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->campTemplate = $campTemplateLoader->getReference(CampTemplateTestData::$TYPE1);
        $this->activityCategoryTemplate = $activityCategoryTemplateLoader->getReference(ActivityCategoryTemplateTestData::$TYPE1);

        $this->authenticateUser($this->user);
    }

    public function testFetch() {
        $this->dispatch("{$this->apiEndpoint}/{$this->activityCategoryTemplate->getId()}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "id": "{$this->activityCategoryTemplate->getId()}",
                "short": "AT",
                "name": "ActivityType1",
                "color": "#FF00FF",
                "numberingStyle": "i"
            }
JSON;

        $expectedLinks = <<<JSON
            {
                "self": {
                    "href": "http://{$this->host}{$this->apiEndpoint}/{$this->activityCategoryTemplate->getId()}"
                }
            }
JSON;
        $expectedEmbeddedObjects = ['contentTypeConfigTemplates'];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testFetchAll() {
        $campTemplateId = $this->campTemplate->getId();
        $this->dispatch("{$this->apiEndpoint}?page_size=10&campTemplateId={$campTemplateId}", 'GET');

        $this->assertResponseStatusCode(200);

        $this->assertEquals(1, $this->getResponseContent()->total_items);
        $this->assertEquals(10, $this->getResponseContent()->page_size);
        $this->assertEquals("http://{$this->host}{$this->apiEndpoint}?page_size=10&campTemplateId={$campTemplateId}&page=1", $this->getResponseContent()->_links->self->href);
        $this->assertEquals($this->activityCategoryTemplate->getId(), $this->getResponseContent()->_embedded->items[0]->id);
    }

    public function testCreateForbidden() {
        $this->dispatch("{$this->apiEndpoint}", 'POST');
        $this->assertResponseStatusCode(405);
    }

    public function testPatchForbidden() {
        $this->dispatch("{$this->apiEndpoint}/{$this->activityCategoryTemplate->getId()}", 'PATCH');
        $this->assertResponseStatusCode(405);
    }

    public function testUpdateForbidden() {
        $this->dispatch("{$this->apiEndpoint}/{$this->activityCategoryTemplate->getId()}", 'PUT');
        $this->assertResponseStatusCode(405);
    }

    public function testDeleteForbidden() {
        $this->dispatch("{$this->apiEndpoint}/{$this->activityCategoryTemplate->getId()}", 'DELETE');
        $this->assertResponseStatusCode(405);
    }
}
