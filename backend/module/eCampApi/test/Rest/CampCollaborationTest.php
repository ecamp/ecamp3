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

    protected CampCollaboration $campCollaborationInactive;
    protected CampCollaboration $camp1CampCollaborationEstablishedManager;
    protected CampCollaboration $camp2CampCollaborationEstablishedManager;

    /** @var User */
    protected $user;
    protected User $user2;
    protected User $manager;

    private $apiEndpoint = '/api/camp-collaborations';

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $campCollaborationLoader = new CampCollaborationTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($campCollaborationLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->user2 = $userLoader->getReference(UserTestData::$USER2);
        $this->manager = $userLoader->getReference(UserTestData::$USER4);
        $this->campCollaboration1 = $campCollaborationLoader->getReference(CampCollaborationTestData::$COLLAB1);
        $this->campCollaborationInvited = $campCollaborationLoader->getReference(CampCollaborationTestData::$COLLAB_INVITED);
        $this->campCollaborationInactive = $campCollaborationLoader->getReference(CampCollaborationTestData::$COLLAB_INACTIVE);
        $this->camp1CampCollaborationEstablishedManager = $campCollaborationLoader->getReference(CampCollaborationTestData::$COLLAB_MANAGER);
        $this->camp2CampCollaborationEstablishedManager = $campCollaborationLoader->getReference(CampCollaborationTestData::$CAMP2_USER1_ESTABLISHED_MANAGER);

        $this->authenticateUser($this->user);
    }

    public function testFetch(): void {
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
        ];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testFetchOnlyEmail(): void {
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
        $expectedEmbeddedObjects = ['camp'];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testFetchAll(): void {
        $campId = $this->campCollaboration1->getCamp()->getId();
        $userId = $this->user->getId();
        $this->dispatch("{$this->apiEndpoint}?page_size=10&userId={$userId}&campId={$campId}", 'GET');

        $this->assertResponseStatusCode(200);

        $this->assertEquals(1, $this->getResponseContent()->total_items);
        $this->assertEquals(10, $this->getResponseContent()->page_size);
        $this->assertEquals("http://{$this->host}{$this->apiEndpoint}?page_size=10&userId={$userId}&campId={$campId}&page=1", $this->getResponseContent()->_links->self->href);
        $this->assertEquals($this->campCollaboration1->getId(), $this->getResponseContent()->_embedded->items[0]->id);
    }

    public function testCreateWithoutRole(): void {
        $this->setRequestContent([
            'role' => '', ]); // TODO: Validierung wär nicht zwingend notwendig. Der Service nimmt einfach 'member' als Default

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        $this->assertResponseStatusCode(422);
        $this->assertObjectHasAttribute('isEmpty', $this->getResponseContent()->validation_messages->role);
    }

    public function testCreateWithoutCamp(): void {
        $this->setRequestContent([
            'role' => 'member',
            'campId' => 'xxx', ]);

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        $this->assertResponseStatusCode(422);
        $this->assertObjectHasAttribute('notFound', $this->getResponseContent()->validation_messages->campId);
    }

    public function testCreateDuplicateEntry(): void {
        $this->setRequestContent([
            'role' => 'member',
            'campId' => $this->campCollaboration1->getCamp()->getId(),
        ]);

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        // user already part of the camp
        $this->assertResponseStatusCode(422);
    }

    public function testCreateDuplicateEntryOnlyWithEmail(): void {
        $this->setRequestContent([
            'role' => 'member',
            'campId' => $this->campCollaborationInvited->getCamp()->getId(),
            'user' => null,
            'inviteEmail' => $this->campCollaborationInvited->getInviteEmail(),
        ]);

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        // user already part of the camp
        $this->assertResponseStatusCode(422);
    }

    public function testCreateForYourselfFails() {
        $this->logout();
        $this->authenticateUser($this->user2);
        $this->setRequestContent([
            'role' => 'member',
            'campId' => $this->campCollaboration1->getCamp()->getId(),
            'userId' => $this->user2->getId(),
        ]);

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        /* It's 422 EntityValidationException because the query for the related camp
         * does not find a camp.
         * It does not find the related camp because the ACL is already included in the fetch query for the camp
         */
        $this->assertResponseStatusCode(422);
    }

    public function testCreateFailsWithoutPermissions() {
        $this->logout();
        $this->authenticateUser($this->user2);
        $this->setRequestContent([
            'role' => 'member',
            'campId' => $this->campCollaboration1->getCamp()->getId(),
            'inviteEmail' => 'my.mail@fantasy.com',
            'userId' => null,
        ]);

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        /* It's 422 EntityValidationException because the query for the related camp
         * does not find a camp.
         * It does not find the related camp because the ACL is already included in the fetch query for the camp.
         */
        $this->assertResponseStatusCode(422);
    }

    public function testCreateSuccess(): void {
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

    /**
     * @dataProvider getRoles
     */
    public function testCreateOnlyWithEmail(string $role): void {
        $inviteEmail = 'my.mail@fantasy.com';
        $this->setRequestContent([
            'role' => $role,
            'campId' => $this->campCollaboration1->getCamp()->getId(),
            'inviteEmail' => $inviteEmail,
            'userId' => null,
        ]);

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        $this->assertResponseStatusCode(201);

        $this->assertThat($this->getResponseContent()->status, self::equalTo(CampCollaboration::STATUS_INVITED));
        $this->assertThat($this->getResponseContent()->inviteEmail, self::equalTo($inviteEmail));
        $this->assertThat($this->getResponseContent()->_embedded, self::logicalNot(self::classHasAttribute('user')));
    }

    public static function getRoles(): array {
        return [[CampCollaboration::ROLE_GUEST], [CampCollaboration::ROLE_MANAGER], [CampCollaboration::ROLE_MEMBER]];
    }

    public function testCreateWithEmailOfExistingUser() {
        $inviteEmail = 'my.mail@fantasy.com';
        $user2 = new User();
        $user2->setUsername('test-user2');
        $user2->setTrustedMailAddress($inviteEmail);
        $user2->setRole(User::ROLE_USER);
        $user2->setState(User::STATE_ACTIVATED);

        $this->getEntityManager()->persist($user2);
        $this->getEntityManager()->flush();

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
        $this->assertThat($this->getResponseContent()->_embedded->user->id, self::equalTo($user2->getId()));
    }

    public function testUpdateSuccess(): void {
        $this->setRequestContent([
            'role' => 'manager', ]);

        $this->dispatch("{$this->apiEndpoint}/{$this->campCollaboration1->getId()}", 'PATCH');

        $this->assertResponseStatusCode(200);
        $this->assertEquals('manager', $this->getResponseContent()->role);
    }

    public function testUpdateFailsWithoutPermission(): void {
        $this->logout();
        $this->authenticateUser($this->user2);
        $this->setRequestContent([
            'role' => 'manager',
        ]);

        $this->dispatch("{$this->apiEndpoint}/{$this->campCollaboration1->getId()}", 'PATCH');

        $this->assertResponseStatusCode(403);
    }

    public function testInviteAgain(): void {
        $this->setRequestContent([
            'status' => CampCollaboration::STATUS_INVITED,
        ]);

        $this->dispatch("{$this->apiEndpoint}/{$this->campCollaborationInactive->getId()}", 'PATCH');

        $this->assertResponseStatusCode(200);

        $this->assertEquals(CampCollaboration::STATUS_INVITED, $this->getResponseContent()->status);
    }

    public function testDelete(): void {
        $collaborationToDelete = $this->campCollaborationInactive->getId();
        $this->dispatch("{$this->apiEndpoint}/{$collaborationToDelete}", 'DELETE');

        $this->assertResponseStatusCode(204);

        /** @var CampCollaboration $cc */
        $cc = $this->getEntityManager()->find(CampCollaboration::class, $collaborationToDelete);
        self::assertThat($cc, self::isNull());
    }

    public function testUpdateToInactiveWhenDeleteAndStatusIsInvited(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->campCollaborationInvited->getId()}", 'DELETE');

        $this->assertResponseStatusCode(204);

        /** @var CampCollaboration $cc */
        $cc = $this->getEntityManager()->find(CampCollaboration::class, $this->campCollaborationInvited->getId());
        $this->assertEquals(CampCollaboration::STATUS_INACTIVE, $cc->getStatus());
    }

    public function testUpdateToInactiveWhenDeleteAndStatusIsEstablished(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->campCollaboration1->getId()}", 'DELETE');

        $this->assertResponseStatusCode(204);

        /** @var CampCollaboration $cc */
        $cc = $this->getEntityManager()->find(CampCollaboration::class, $this->campCollaboration1->getId());
        $this->assertEquals(CampCollaboration::STATUS_INACTIVE, $cc->getStatus());
    }

    public function testDeleteFailsWithoutPermissions(): void {
        $this->logout();
        $this->authenticateUser($this->user2);
        $this->dispatch("{$this->apiEndpoint}/{$this->campCollaboration1->getId()}", 'DELETE');

        $this->assertResponseStatusCode(403);
    }

    public function testUpdateRoleToMemberFailsIfNoOtherManager() {
        $this->logout();
        $this->authenticateUser($this->manager);

        $this->setRequestContent([
            'role' => 'member',
        ]);

        $this->dispatch("{$this->apiEndpoint}/{$this->camp1CampCollaborationEstablishedManager->getId()}", 'PATCH');

        $this->assertResponseStatusCode(422);
    }

    public function testDeleteManagerFailsIfNoOtherManager() {
        $this->logout();
        $this->authenticateUser($this->manager);

        $this->dispatch("{$this->apiEndpoint}/{$this->camp1CampCollaborationEstablishedManager->getId()}", 'DELETE');

        $this->assertResponseStatusCode(422);
    }

    public function testUpdateRoleToMemberSucceedsIfOtherManagerExists() {
        $this->setRequestContent([
            'role' => 'member',
        ]);

        $this->dispatch("{$this->apiEndpoint}/{$this->camp2CampCollaborationEstablishedManager->getId()}", 'PATCH');

        $this->assertResponseStatusCode(200);
    }

    public function testDeleteManagerSucceedsIfOtherManagerExists() {
        $this->dispatch("{$this->apiEndpoint}/{$this->camp2CampCollaborationEstablishedManager->getId()}", 'DELETE');

        $this->assertResponseStatusCode(204);
    }
}
