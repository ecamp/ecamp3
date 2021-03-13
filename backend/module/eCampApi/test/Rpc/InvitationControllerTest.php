<?php

namespace eCamp\ApiTest\V1\Rpc\Invitation;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Entity\User;
use eCamp\CoreTest\Data\CampCollaborationTestData;
use eCamp\CoreTest\Data\CampTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class InvitationControllerTest extends AbstractApiControllerTestCase {
    /** @var User */
    protected $userAlreadyInCamp;

    /** @var User */
    private $newUser;

    private $apiEndpoint = '/api/invitations';
    private $camp;
    /**
     * @var CampCollaboration
     */
    private $campCollaborationInvited;

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $campLoader = new CampTestData();
        $campCollaborationTestData = new CampCollaborationTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($campLoader);
        $loader->addFixture($campCollaborationTestData);
        $this->loadFixtures($loader);

        $this->userAlreadyInCamp = $userLoader->getReference(UserTestData::$USER1);
        $this->newUser = $userLoader->getReference(UserTestData::$USER2);
        $this->camp = $campLoader->getReference(CampTestData::$CAMP1);
        $this->campCollaborationInvited = $campCollaborationTestData->getReference(CampCollaborationTestData::$COLLAB_INVITED);
    }

    public function testNotFoundForNotMatchingInviteKey(): void {
        $this->dispatch("{$this->apiEndpoint}/doesNotExist/find", 'GET');

        $this->assertResponseStatusCode(404);
    }

    public function testFindsInvitation(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->campCollaborationInvited->getInviteKey()}/find", 'GET');

        $this->assertResponseStatusCode(200);

        $camp = $this->campCollaborationInvited->getCamp();
        $expectedBody = <<<JSON
            {
                "campId": "{$camp->getId()}",
                "campTitle": "{$camp->getTitle()}",
                "userDisplayName": null,
                "userAlreadyInCamp": null
            }
JSON;

        $expectedLinks = <<<'JSON'
        {
           "self":{
              "href":"http:///api/invitations/find"
           }
        }
JSON;
        $expectedEmbeddedObjects = [];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testFindsInvitationWhenLoggedIn(): void {
        $this->authenticateUser($this->newUser);
        $this->dispatch("{$this->apiEndpoint}/{$this->campCollaborationInvited->getInviteKey()}/find", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "campId": "{$this->campCollaborationInvited->getCamp()->getId()}",
                "campTitle": "{$this->campCollaborationInvited->getCamp()->getTitle()}",
                "userDisplayName": "{$this->newUser->getDisplayName()}",
                "userAlreadyInCamp": false
            }
JSON;

        $expectedLinks = <<<'JSON'
        {
           "self":{
              "href":"http:///api/invitations/find"
           }
        }
JSON;
        $expectedEmbeddedObjects = [];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testFindsInvitationWhenAlreadyInCamp(): void {
        $this->authenticateUser($this->userAlreadyInCamp);
        $this->dispatch("{$this->apiEndpoint}/{$this->campCollaborationInvited->getInviteKey()}/find", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "campId": "{$this->campCollaborationInvited->getCamp()->getId()}",
                "campTitle": "{$this->campCollaborationInvited->getCamp()->getTitle()}",
                "userDisplayName": "{$this->userAlreadyInCamp->getDisplayName()}",
                "userAlreadyInCamp": true
            }
JSON;

        $expectedLinks = <<<'JSON'
        {
           "self":{
              "href":"http:///api/invitations/find"
           }
        }
JSON;
        $expectedEmbeddedObjects = [];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testAcceptFailsWhenGetRequest(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->campCollaborationInvited->getInviteKey()}/accept", 'GET');

        $this->assertResponseStatusCode(405);
    }

    public function testRejectFailsWhenGetRequest(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->campCollaborationInvited->getInviteKey()}/reject", 'GET');

        $this->assertResponseStatusCode(405);
    }

    public function testAcceptFailsWhenNotAuthenticated(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->campCollaborationInvited->getInviteKey()}/accept", 'POST');

        $this->assertResponseStatusCode(401);
    }

    public function testAcceptInvitationWithUserAlreadyInCamp(): void {
        $this->authenticateUser($this->userAlreadyInCamp);
        $this->dispatch("{$this->apiEndpoint}/{$this->campCollaborationInvited->getInviteKey()}/accept", 'POST');

        $this->assertResponseStatusCode(422);
    }

    public function testAcceptInvitation(): void {
        $this->authenticateUser($this->newUser);
        $this->dispatch("{$this->apiEndpoint}/{$this->campCollaborationInvited->getInviteKey()}/accept", 'POST');

        $this->assertResponseStatusCode(200);
    }

    public function testRejectInvitation(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->campCollaborationInvited->getInviteKey()}/reject", 'POST');

        $this->assertResponseStatusCode(200);
    }
}
