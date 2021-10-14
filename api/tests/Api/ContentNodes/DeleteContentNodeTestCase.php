<?php

namespace App\Tests\Api\ContentNodes;

use App\Entity\ContentNode;
use App\Entity\User;
use App\Tests\Api\ECampApiTestCase;

/**
 * Base UPDATE (patch) test case to be used for various ContentNode types.
 *
 * This test class covers all tests that are the same across all content node implementations
 *
 * @internal
 */
abstract class DeleteContentNodeTestCase extends ECampApiTestCase {
    protected $defaultContentNode;

    protected $endpoint = '';

    public function testDeleteIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('DELETE', "/content_node/{$this->endpoint}/".$this->defaultContentNode->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);

        $this->assertEntityStillExists();
    }

    public function testDeleteIsDeniedForInvitedCollaborator() {
        $this->delete(user: static::$fixtures['user6invited']);
        $this->assertResponseStatusCodeSame(404);
        $this->assertEntityStillExists();
    }

    public function testDeleteIsDeniedForInactiveCollaborator() {
        $this->delete(user: static::$fixtures['user5inactive']);
        $this->assertResponseStatusCodeSame(404);
        $this->assertEntityStillExists();
    }

    public function testDeleteIsDeniedForUnrelatedUser() {
        $this->delete(user: static::$fixtures['user4unrelated']);
        $this->assertResponseStatusCodeSame(404);
        $this->assertEntityStillExists();
    }

    public function testDeleteIsDeniedForGuest() {
        $this->delete(user: static::$fixtures['user3guest']);
        $this->assertResponseStatusCodeSame(403);
        $this->assertEntityStillExists();
    }

    public function testDeleteIsAllowedForMember() {
        $this->delete(user: static::$fixtures['user2member']);
        $this->assertResponseStatusCodeSame(204);
        $this->assertEntityWasRemoved();
    }

    public function testDeleteIsAllowedForManager() {
        $this->delete(user: static::$fixtures['user1manager']);
        $this->assertResponseStatusCodeSame(204);
        $this->assertEntityWasRemoved();
    }

    protected function delete(?ContentNode $contentNode = null, ?User $user = null) {
        $credentials = null;
        if (null !== $user) {
            $credentials = ['username' => $user->getUsername()];
        }

        $contentNode ??= $this->defaultContentNode;

        static::createClientWithCredentials($credentials)->request('DELETE', "/content_node/{$this->endpoint}/".$this->defaultContentNode->getId());
    }

    protected function assertEntityWasRemoved(?ContentNode $contentNode = null) {
        $contentNode ??= $this->defaultContentNode;

        $this->assertNull($this->getEntityManager()->getRepository(get_class($contentNode))->find($contentNode->getId()));
    }

    protected function assertEntityStillExists(?ContentNode $contentNode = null) {
        $contentNode ??= $this->defaultContentNode;

        $this->assertNotNull($this->getEntityManager()->getRepository(get_class($contentNode))->find($contentNode->getId()));
    }
}
