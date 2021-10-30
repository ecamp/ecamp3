<?php

namespace App\Tests\Api\ContentNodes;

use App\Entity\ContentNode;
use App\Tests\Api\ECampApiTestCase;

/**
 * Base READ (get) test case to be used for various ContentNode types.
 *
 * This test class covers all tests that are the same across all content node implementations
 *
 * @internal
 */
abstract class ReadContentNodeTestCase extends ECampApiTestCase {
    public function testGetIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('GET', "{$this->endpoint}/".$this->defaultEntity->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testGetIsDeniedForInvitedCollaborator() {
        $this->get(user: static::$fixtures['user6invited']);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetIsDeniedForInactiveCollaborator() {
        $this->get(user: static::$fixtures['user5inactive']);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetIsDeniedForUnrelatedUser() {
        $this->get(user: static::$fixtures['user4unrelated']);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetIsAllowedForGuest() {
        $this->get(user: static::$fixtures['user3guest']);
        $this->assertResponseStatusCodeSame(200);
    }

    public function testGetIsAllowedForMember() {
        $this->get(user: static::$fixtures['user2member']);
        $this->assertResponseStatusCodeSame(200);
    }

    public function testGetIsAllowedForManager() {
        $this->get(user: static::$fixtures['user1manager']);
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            'id' => $this->defaultEntity->getId(),
            'instanceName' => $this->defaultEntity->instanceName,
            'slot' => $this->defaultEntity->slot,
            'position' => $this->defaultEntity->position,
            'contentTypeName' => $this->defaultEntity->getContentTypeName(),

            '_links' => [
                'parent' => ['href' => $this->getIriFor($this->defaultEntity->parent)],
                'owner' => ['href' => $this->getIriFor('activity1')],
                'ownerCategory' => ['href' => $this->getIriFor('category1')],
            ],
        ]);
    }
}
