<?php

namespace App\Tests\Api\ContentNodes;

use App\Entity\ContentNode;
use App\Tests\Api\ECampApiTestCase;

/**
 * Base DELETE test case to be used for various ContentNode types.
 *
 * This test class covers all tests that are the same across all content node implementations
 *
 * @internal
 */
abstract class DeleteContentNodeTestCase extends ECampApiTestCase {
    public function testDeleteIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('DELETE', "{$this->endpoint}/".$this->defaultEntity->getId());
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
}
