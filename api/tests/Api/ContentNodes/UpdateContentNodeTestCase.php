<?php

namespace App\Tests\Api\ContentNodes;

use App\Tests\Api\ECampApiTestCase;

/**
 * Base UPDATE (patch) test case to be used for various ContentNode types.
 *
 * This test class covers all tests that are the same across all content node implementations
 *
 * @internal
 */
abstract class UpdateContentNodeTestCase extends ECampApiTestCase {
    protected $defaultContentNode;

    protected $endpoint = '';

    public function testPatchIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('PATCH', "/content_node/{$this->endpoint}/".$this->defaultContentNode->getId(), ['json' => [], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testPatchIsDeniedForInvitedCollaborator() {
        $this->patch($this->defaultContentNode, [], static::$fixtures['user6invited']);
        $this->assertResponseStatusCodeSame(403);
    }

    public function testPatchIsDeniedForInactiveCollaborator() {
        $this->patch($this->defaultContentNode, [], static::$fixtures['user5inactive']);
        $this->assertResponseStatusCodeSame(403);
    }

    public function testPatchIsDeniedForUnrelatedUser() {
        $this->patch($this->defaultContentNode, [], static::$fixtures['user4unrelated']);
        $this->assertResponseStatusCodeSame(403);
    }

    public function testPatchIsDeniedForGuest() {
        $this->patch($this->defaultContentNode, [], static::$fixtures['user3guest']);
        $this->assertResponseStatusCodeSame(403);
    }

    public function testPatchIsAllowedForMember() {
        $this->patch($this->defaultContentNode, [], static::$fixtures['user2member']);
        $this->assertResponseStatusCodeSame(200);
    }

    public function testPatchIsAllowedForManager() {
        $this->patch($this->defaultContentNode, [], static::$fixtures['user1manager']);
        $this->assertResponseStatusCodeSame(200);
    }

    protected function patch($contentNode, $payload = [], $user = null) {
        $credentials = null !== $user ? ['username' => $user->getUsername()] : null;
        static::createClientWithCredentials($credentials)->request('PATCH', "/content_node/{$this->endpoint}/".$this->defaultContentNode->getId(), ['json' => $payload, 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
    }
}
