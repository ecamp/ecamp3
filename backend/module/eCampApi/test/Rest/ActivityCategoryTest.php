<?php

namespace eCamp\ApiTest\Rest;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\ActivityCategory;
use eCamp\Core\Entity\User;
use eCamp\CoreTest\Data\ActivityCategoryTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class ActivityCategoryTest extends AbstractApiControllerTestCase {
    /** @var ActivityCategory */
    protected $activityCategory;

    /** @var User */
    protected $user;

    private $apiEndpoint = '/api/activity-categories';

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $activityCategoryLoader = new ActivityCategoryTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($activityCategoryLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->activityCategory = $activityCategoryLoader->getReference(ActivityCategoryTestData::$CATEGORY1);

        $this->authenticateUser($this->user);
    }

    public function testFetch() {
        $this->dispatch("{$this->apiEndpoint}/{$this->activityCategory->getId()}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "id": "{$this->activityCategory->getId()}",
                "short": "LS",
                "name": "ActivityCategory1",
                "color": "#FF9800",
                "numberingStyle": "i"
            }
JSON;

        $expectedLinks = <<<JSON
            {
                "self": {
                    "href": "http://{$this->host}{$this->apiEndpoint}/{$this->activityCategory->getId()}"
                }
            }
JSON;
        $expectedEmbeddedObjects = ['camp', 'activityType'];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testFetchAll() {
        $campId = $this->activityCategory->getCamp()->getId();
        $this->dispatch("{$this->apiEndpoint}?page_size=10&campId={$campId}", 'GET');

        $this->assertResponseStatusCode(200);

        $this->assertEquals(2, $this->getResponseContent()->total_items);
        $this->assertEquals(10, $this->getResponseContent()->page_size);
        $this->assertEquals("http://{$this->host}{$this->apiEndpoint}?page_size=10&campId={$campId}&page=1", $this->getResponseContent()->_links->self->href);
        $this->assertEquals($this->activityCategory->getId(), $this->getResponseContent()->_embedded->items[0]->id);
    }

    public function testCreateWithoutParams() {
        $this->setRequestContent([
            'name' => '', ]);

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        $this->assertResponseStatusCode(422);
        $this->assertObjectHasAttribute('isEmpty', $this->getResponseContent()->validation_messages->name);
        $this->assertObjectHasAttribute('isEmpty', $this->getResponseContent()->validation_messages->short);
        $this->assertObjectHasAttribute('isEmpty', $this->getResponseContent()->validation_messages->color);
        $this->assertObjectHasAttribute('isEmpty', $this->getResponseContent()->validation_messages->numberingStyle);
    }

    public function testCreateWithoutCamp() {
        $this->setRequestContent([
            'name' => 'ActivityCategory2',
            'short' => 'AC2',
            'color' => '#FFFFFF',
            'numberingStyle' => 'i',
            'activityTypeId' => $this->activityCategory->getActivityType()->getId(),
            'campId' => 'xxx', ]);

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        $this->assertResponseStatusCode(422);
        $this->assertObjectHasAttribute('notFound', $this->getResponseContent()->validation_messages->campId);
    }

    public function testCreateSuccess() {
        $this->setRequestContent([
            'name' => 'ActivityCategory2',
            'short' => 'AC2',
            'color' => '#FFFFFF',
            'numberingStyle' => 'i',
            'activityTypeId' => $this->activityCategory->getActivityType()->getId(),
            'campId' => $this->activityCategory->getCamp()->getId(), ]);

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        $this->assertResponseStatusCode(201);
        $this->assertEquals('ActivityCategory2', $this->getResponseContent()->name);
    }

    public function testUpdateSuccess() {
        $this->setRequestContent([
            'name' => 'ActivityCategory3', ]);

        $this->dispatch("{$this->apiEndpoint}/{$this->activityCategory->getId()}", 'PATCH');

        $this->assertResponseStatusCode(200);

        $this->assertEquals('ActivityCategory3', $this->getResponseContent()->name);
    }

    public function testDelete() {
        $this->dispatch("{$this->apiEndpoint}/{$this->activityCategory->getId()}", 'DELETE');

        $this->assertResponseStatusCode(204);

        $result = $this->getEntityManager()->find(ActivityCategory::class, $this->activityCategory->getId());
        $this->assertNull($result);
    }
}
