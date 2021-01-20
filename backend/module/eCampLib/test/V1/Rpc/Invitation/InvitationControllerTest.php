<?php

namespace eCamp\LibTest\V1\Rpc\Invitation;

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
    protected $user;

    private $apiEndpoint = '/api/invitation';
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

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->camp = $campLoader->getReference(CampTestData::$CAMP1);
        $this->campCollaborationInvited = $campCollaborationTestData->getReference(CampCollaborationTestData::$COLLAB_INVITED);
    }

    public function testNotFoundForNotMatchingInviteKey() {
        $this->dispatch("{$this->apiEndpoint}/find/doesNotExist", 'GET');

        $this->assertResponseStatusCode(404);
    }

    public function testFindsInvitation() {
        $this->dispatch("{$this->apiEndpoint}/find/{$this->campCollaborationInvited->getInviteKey()}", 'GET');

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
           "self":{
              "href":"http:///api/camp-collaborations/{$this->campCollaborationInvited->getId()}"
           }
        }
JSON;
        $expectedEmbeddedObjects = ['camp'];

        $responseContent = $this->getResponseContent();
        self::assertThat($responseContent->_embedded->camp->title, self::logicalNot(self::isEmpty()));

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testAcceptFailsWhenGetRequest() {
        $this->dispatch("{$this->apiEndpoint}/accept/{$this->campCollaborationInvited->getInviteKey()}", 'GET');

        $this->assertResponseStatusCode(400);
    }

    public function testRejectFailsWhenGetRequest() {
        $this->dispatch("{$this->apiEndpoint}/reject/{$this->campCollaborationInvited->getInviteKey()}", 'GET');

        $this->assertResponseStatusCode(400);
    }

    public function testAcceptFailsWhenNotAuthenticated() {
        $this->dispatch("{$this->apiEndpoint}/accept/{$this->campCollaborationInvited->getInviteKey()}", 'POST');

        $this->assertResponseStatusCode(401);
    }

    public function testAcceptInvitation() {
        $this->authenticateUser($this->user);
        $this->dispatch("{$this->apiEndpoint}/accept/{$this->campCollaborationInvited->getInviteKey()}", 'POST');

        $this->assertResponseStatusCode(200);
    }

    public function testRejectInvitation() {
        $this->dispatch("{$this->apiEndpoint}/reject/{$this->campCollaborationInvited->getInviteKey()}", 'POST');

        $this->assertResponseStatusCode(200);
    }
}
