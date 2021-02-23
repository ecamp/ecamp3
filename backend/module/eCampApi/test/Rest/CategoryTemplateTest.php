<?php

namespace eCamp\ApiTest\Rest;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\CampTemplate;
use eCamp\Core\Entity\CategoryTemplate;
use eCamp\Core\Entity\User;
use eCamp\CoreTest\Data\CampTemplateTestData;
use eCamp\CoreTest\Data\CategoryTemplateTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class CategoryTemplateTest extends AbstractApiControllerTestCase {
    /** @var CategoryTemplate */
    protected $categoryTemplate;

    /** @var CampTemplate */
    protected $campTemplate;

    /** @var User */
    protected $user;

    private $apiEndpoint = '/api/category-templates';

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $campTemplateLoader = new CampTemplateTestData();
        $categoryTemplateLoader = new CategoryTemplateTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($campTemplateLoader);
        $loader->addFixture($categoryTemplateLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->campTemplate = $campTemplateLoader->getReference(CampTemplateTestData::$TEMPLATE1);
        $this->categoryTemplate = $categoryTemplateLoader->getReference(CategoryTemplateTestData::$TEMPLATE1);

        $this->authenticateUser($this->user);
    }

    public function testFetch(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->categoryTemplate->getId()}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "id": "{$this->categoryTemplate->getId()}",
                "short": "AC",
                "name": "ActivityCategory1",
                "color": "#FF00FF",
                "numberingStyle": "i"
            }
JSON;

        $expectedLinks = <<<JSON
            {
                "self": {
                    "href": "http://{$this->host}{$this->apiEndpoint}/{$this->categoryTemplate->getId()}"
                }
            }
JSON;
        $expectedEmbeddedObjects = ['categoryContentTypeTemplates', 'categoryContentTemplates'];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testFetchAll(): void {
        $campTemplateId = $this->campTemplate->getId();
        $this->dispatch("{$this->apiEndpoint}?page_size=10&campTemplateId={$campTemplateId}", 'GET');

        $this->assertResponseStatusCode(200);

        $this->assertEquals(1, $this->getResponseContent()->total_items);
        $this->assertEquals(10, $this->getResponseContent()->page_size);
        $this->assertEquals("http://{$this->host}{$this->apiEndpoint}?page_size=10&campTemplateId={$campTemplateId}&page=1", $this->getResponseContent()->_links->self->href);
        $this->assertEquals($this->categoryTemplate->getId(), $this->getResponseContent()->_embedded->items[0]->id);
    }

    public function testCreateForbidden(): void {
        $this->dispatch("{$this->apiEndpoint}", 'POST');
        $this->assertResponseStatusCode(405);
    }

    public function testPatchForbidden(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->categoryTemplate->getId()}", 'PATCH');
        $this->assertResponseStatusCode(405);
    }

    public function testUpdateForbidden(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->categoryTemplate->getId()}", 'PUT');
        $this->assertResponseStatusCode(405);
    }

    public function testDeleteForbidden(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->categoryTemplate->getId()}", 'DELETE');
        $this->assertResponseStatusCode(405);
    }
}
