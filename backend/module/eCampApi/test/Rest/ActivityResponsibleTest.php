<?php

namespace eCamp\ApiTest\Rest;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\ActivityResponsible;
use eCamp\Core\Entity\User;
use eCamp\CoreTest\Data\ActivityResponsibleTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class ActivityResponsibleTest extends AbstractApiControllerTestCase {
    /** @var ActivityResponsible */
    protected $activityResponsible;

    /** @var User */
    protected $user;

    private $apiEndpoint = '/api/activity-responsibles';

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $activityResponsibleLoader = new ActivityResponsibleTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($activityResponsibleLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->activityResponsible = $activityResponsibleLoader->getReference(ActivityResponsibleTestData::$RESPONSIBLE1);

        $this->authenticateUser($this->user);
    }

    public function testFetch(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->activityResponsible->getId()}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "id": "{$this->activityResponsible->getId()}"
            }
JSON;

        $expectedLinks = <<<JSON
            {
                "self": {
                    "href": "http://{$this->host}{$this->apiEndpoint}/{$this->activityResponsible->getId()}"
                }
            }
JSON;
        $expectedEmbeddedObjects = ['campCollaboration', 'activity'];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testFetchAll(): void {
        $activityId = $this->activityResponsible->getActivity()->getId();
        $campCollaborationId = $this->activityResponsible->getCampCollaboration()->getId();

        $this->dispatch("{$this->apiEndpoint}?page_size=10&activityId={$activityId}&campCollaborationId={$campCollaborationId}", 'GET');

        $this->assertResponseStatusCode(200);

        $this->assertEquals(1, $this->getResponseContent()->total_items);
        $this->assertEquals(10, $this->getResponseContent()->page_size);
        $this->assertEquals("http://{$this->host}{$this->apiEndpoint}?page_size=10&activityId={$activityId}&campCollaborationId={$campCollaborationId}&page=1", $this->getResponseContent()->_links->self->href);
        $this->assertEquals($this->activityResponsible->getId(), $this->getResponseContent()->_embedded->items[0]->id);
    }

    public function testCreateDuplicateEntry(): void {
        $this->setRequestContent([
            'activityId' => $this->activityResponsible->getActivity()->getId(),
            'campCollaborationId' => $this->activityResponsible->getCampCollaboration()->getId(), ]);

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        $this->assertResponseStatusCode(500);
    }

    public function testCreateSuccess(): void {
        $activity = new Activity();
        $activity->setCamp($this->activityResponsible->getCamp());
        $activity->setTitle('Activity1');
        $activity->setCategory($this->activityResponsible->getActivity()->getCategory());

        $this->getEntityManager()->persist($activity);
        $this->getEntityManager()->flush();

        $this->setRequestContent([
            'activityId' => $activity->getId(),
            'campCollaborationId' => $this->activityResponsible->getCampCollaboration()->getId(), ]);

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        $this->assertResponseStatusCode(201);
    }

    public function testUpdateSuccess(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->activityResponsible->getId()}", 'PATCH');

        // nothing worth updating on this entity
        $this->assertResponseStatusCode(405);
    }

    public function testDelete(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->activityResponsible->getId()}", 'DELETE');

        $this->assertResponseStatusCode(204);

        $result = $this->getEntityManager()->find(ActivityResponsible::class, $this->activityResponsible->getId());
        $this->assertNull($result);
    }
}
