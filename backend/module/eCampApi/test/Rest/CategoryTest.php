<?php

namespace eCamp\ApiTest\Rest;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\Category;
use eCamp\Core\Entity\ContentType;
use eCamp\Core\Entity\User;
use eCamp\CoreTest\Data\CategoryContentTypeTestData;
use eCamp\CoreTest\Data\CategoryTestData;
use eCamp\CoreTest\Data\ContentTypeTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class CategoryTest extends AbstractApiControllerTestCase {
    /** @var Category */
    protected $category;

    /** @var User */
    protected $user;

    /** @var ContentType */
    protected $contentType;

    private $apiEndpoint = '/api/categories';

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $categoryLoader = new CategoryTestData();
        $categoryContentTypeLoader = new CategoryContentTypeTestData();
        $contentTypeLoader = new ContentTypeTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($categoryLoader);
        $loader->addFixture($contentTypeLoader);
        $loader->addFixture($categoryContentTypeLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->category = $categoryLoader->getReference(CategoryTestData::$CATEGORY1);
        $this->contentType = $contentTypeLoader->getReference(ContentTypeTestData::$TYPE1);

        $this->authenticateUser($this->user);
    }

    public function testFetch(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->category->getId()}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "id": "{$this->category->getId()}",
                "short": "LS",
                "name": "ActivityCategory1",
                "color": "#FF9800",
                "numberingStyle": "i",
                "rootContentNode": null
            }
JSON;

        $expectedLinks = <<<JSON
            {
                "self": {
                    "href": "http://{$this->host}{$this->apiEndpoint}/{$this->category->getId()}"
                }
            }
JSON;
        $expectedEmbeddedObjects = ['camp', 'contentNodes', 'preferredContentTypes'];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testFetchAll(): void {
        $campId = $this->category->getCamp()->getId();
        $this->dispatch("{$this->apiEndpoint}?page_size=10&campId={$campId}", 'GET');

        $this->assertResponseStatusCode(200);

        $this->assertEquals(2, $this->getResponseContent()->total_items);
        $this->assertEquals(10, $this->getResponseContent()->page_size);
        $this->assertEquals("http://{$this->host}{$this->apiEndpoint}?page_size=10&campId={$campId}&page=1", $this->getResponseContent()->_links->self->href);
        $this->assertEquals($this->category->getId(), $this->getResponseContent()->_embedded->items[0]->id);
    }

    public function testCreateWithoutParams(): void {
        $this->setRequestContent(['name' => '']);

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        $this->assertResponseStatusCode(422);
        $this->assertObjectHasAttribute('isEmpty', $this->getResponseContent()->validation_messages->name);
        $this->assertObjectHasAttribute('isEmpty', $this->getResponseContent()->validation_messages->short);
        $this->assertObjectHasAttribute('isEmpty', $this->getResponseContent()->validation_messages->color);
        $this->assertObjectHasAttribute('isEmpty', $this->getResponseContent()->validation_messages->numberingStyle);
    }

    public function testCreateWithoutCamp(): void {
        $this->setRequestContent([
            'name' => 'ActivityCategory2',
            'short' => 'AC2',
            'color' => '#FFFFFF',
            'numberingStyle' => 'i',
            'campId' => 'xxx',
        ]);

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        $this->assertResponseStatusCode(422);
        $this->assertObjectHasAttribute('notFound', $this->getResponseContent()->validation_messages->campId);
    }

    public function testCreateSuccess(): void {
        $this->setRequestContent([
            'name' => 'ActivityCategory2',
            'short' => 'AC2',
            'color' => '#FFFFFF',
            'numberingStyle' => 'i',
            'campId' => $this->category->getCamp()->getId(), ]);

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        $this->assertResponseStatusCode(201);
        $this->assertEquals('ActivityCategory2', $this->getResponseContent()->name);
    }

    public function testUpdateSuccess(): void {
        $this->setRequestContent([
            'name' => 'ActivityCategory3', ]);

        $this->dispatch("{$this->apiEndpoint}/{$this->category->getId()}", 'PATCH');

        $this->assertResponseStatusCode(200);

        $this->assertEquals('ActivityCategory3', $this->getResponseContent()->name);
    }

    public function testDelete(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->category->getId()}", 'DELETE');

        $this->assertResponseStatusCode(204);

        $result = $this->getEntityManager()->find(Category::class, $this->category->getId());
        $this->assertNull($result);
    }

    public function testClearPreferredContentTypes(): void {
        $this->setRequestContent(['preferredContentTypes' => []]);

        $this->dispatch("{$this->apiEndpoint}/{$this->category->getId()}", 'PATCH');

        $this->assertResponseStatusCode(200);

        $this->assertCount(0, $this->getResponseContent()->_embedded->preferredContentTypes);
    }

    public function testSetPreferredContentTypes(): void {
        $this->setRequestContent([
            'preferredContentTypes' => [['id' => $this->contentType->getId()]],
        ]);

        $this->dispatch("{$this->apiEndpoint}/{$this->category->getId()}", 'PATCH');

        $this->assertResponseStatusCode(200);

        $this->assertCount(1, $this->getResponseContent()->_embedded->preferredContentTypes);
    }
}
