<?php

namespace eCamp\ApiTest\Rest;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Entity\User;
use eCamp\CoreTest\Data\CampCollaborationTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class CampCollaborationTest extends AbstractApiControllerTestCase {
    /** @var CampCollaboration */
    protected $campCollaboration1;
    /** @var CampCollaboration */
    protected $campCollaborationInvited;

    /** @var User */
    protected $user;

    private $apiEndpoint = '/api/camp-collaborations';

    public function setUp() {
        parent::setUp();

        $userLoader = new UserTestData();
        $campCollaborationLoader = new CampCollaborationTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($campCollaborationLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->campCollaboration1 = $campCollaborationLoader->getReference(CampCollaborationTestData::$COLLAB1);
        $this->campCollaborationInvited = $campCollaborationLoader->getReference(CampCollaborationTestData::$COLLAB_INVITED);

        $this->authenticateUser($this->user);
    }

    public function testFetch() {
        $this->dispatch("{$this->apiEndpoint}/{$this->campCollaboration1->getId()}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "id": "{$this->campCollaboration1->getId()}",
                "role": "member",
                "status": "established",
                "inviteEmail": null
            }
JSON;

        $expectedLinks = <<<JSON
            {
                "self": {
                    "href": "http://{$this->host}{$this->apiEndpoint}/{$this->campCollaboration1->getId()}"
                }
            }
JSON;
        $expectedEmbeddedObjects = [
            'camp',
            'user',
        ]; // TODO discuss: wouldn't 'campCollaborationResponsibles' be more intuitive than 'campCollaborations'

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testFetchOnlyEmail() {
        $this->dispatch("{$this->apiEndpoint}/{$this->campCollaborationInvited->getId()}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "id": "{$this->campCollaborationInvited->getId()}",
                "role": "{$this->campCollaborationInvited->getRole()}",
                "status": "{$this->campCollaborationInvited->getStatus()}",
                "inviteEmail": "{$this->campCollaborationInvited->getInviteEmail()}",
                "user": null
            }
JSON;

        $expectedLinks = <<<JSON
            {
                "self": {
                    "href": "http://{$this->host}{$this->apiEndpoint}/{$this->campCollaborationInvited->getId()}"
                }
            }
JSON;
        $expectedEmbeddedObjects = ['camp']; // TODO discuss: wouldn't 'campCollaborationResponsibles' be more intuitive than 'campCollaborations'

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testFetchAll() {
        $campId = $this->campCollaboration1->getCamp()->getId();
        $userId = $this->user->getId();
        $this->dispatch("{$this->apiEndpoint}?page_size=10&userId={$userId}&campId={$campId}", 'GET');

        $this->assertResponseStatusCode(200);

        $this->assertEquals(1, $this->getResponseContent()->total_items);
        $this->assertEquals(10, $this->getResponseContent()->page_size);
        $this->assertEquals("http://{$this->host}{$this->apiEndpoint}?page_size=10&userId={$userId}&campId={$campId}&page=1", $this->getResponseContent()->_links->self->href);
        $this->assertEquals($this->campCollaboration1->getId(), $this->getResponseContent()->_embedded->items[0]->id);
    }

    public function testCreateWithoutRole() {
        $this->setRequestContent([
            'role' => '', ]); // TODO: Validierung wär nicht zwingend notwendig. Der Service nimmt einfach 'member' als Default

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        $this->assertResponseStatusCode(422);
        $this->assertObjectHasAttribute('isEmpty', $this->getResponseContent()->validation_messages->role);
    }

    public function testCreateWithoutCamp() {
        $this->setRequestContent([
            'role' => 'member',
            'campId' => 'xxx', ]);

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        $this->assertResponseStatusCode(422);
        $this->assertObjectHasAttribute('notFound', $this->getResponseContent()->validation_messages->campId);
    }

    public function testCreateDuplicateEntry() {
        $this->setRequestContent([
            'role' => 'member',
            'campId' => $this->campCollaboration1->getCamp()->getId(),
        ]);

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        // user already part of the camp
        $this->assertResponseStatusCode(500);
    }

    public function testCreateDuplicateEntryOnlyWithEmail() {
        $this->setRequestContent([
            'role' => 'member',
            'campId' => $this->campCollaborationInvited->getCamp()->getId(),
            'user' => null,
            'inviteEmail' => $this->campCollaborationInvited->getInviteEmail(),
        ]);

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        // user already part of the camp
        $this->assertResponseStatusCode(500);
    }

    public function testCreateSuccess() {
        $user2 = new User();
        $user2->setUsername('test-user2');
        $user2->setRole(User::ROLE_USER);
        $user2->setState(User::STATE_ACTIVATED);

        $this->getEntityManager()->persist($user2);
        $this->getEntityManager()->flush();

        $this->setRequestContent([
            'role' => 'member',
            'campId' => $this->campCollaboration1->getCamp()->getId(),
            'userId' => $user2->getId(), ]);

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        $this->assertResponseStatusCode(201);

        $this->assertEquals(CampCollaboration::STATUS_INVITED, $this->getResponseContent()->status);
    }

    public function testCreateOnlyWithEmail() {
        $inviteEmail = 'my.mail@fantasy.com';
        $this->setRequestContent([
            'role' => CampCollaboration::ROLE_MEMBER,
            'campId' => $this->campCollaboration1->getCamp()->getId(),
            'inviteEmail' => $inviteEmail,
            'userId' => null,
        ]);

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        $this->assertResponseStatusCode(201);

        $this->assertThat($this->getResponseContent()->status, self::equalTo(CampCollaboration::STATUS_INVITED));
        $this->assertThat($this->getResponseContent()->inviteEmail, self::equalTo($inviteEmail));
        $this->assertThat($this->getResponseContent()->user, self::isNull());
    }

    public function testUpdateSuccess() {
        $this->setRequestContent([
            'role' => 'manager', ]);

        $this->dispatch("{$this->apiEndpoint}/{$this->campCollaboration1->getId()}", 'PATCH');

        $this->assertResponseStatusCode(200);

        // TODO: this should not be posible (implement ACL & write tests for it)
        $this->assertEquals('manager', $this->getResponseContent()->role);
    }

    public function testDelete() {
        $this->dispatch("{$this->apiEndpoint}/{$this->campCollaboration1->getId()}", 'DELETE');

        $this->assertResponseStatusCode(204);

        /** @var CampCollaboration $cc */
        $cc = $this->getEntityManager()->find(CampCollaboration::class, $this->campCollaboration1->getId());
        $this->assertEquals(CampCollaboration::STATUS_LEFT, $cc->getStatus());
    }
}
