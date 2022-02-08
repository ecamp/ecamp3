<?php

namespace App\Tests\Api\ContentNodes;

use App\Entity\ContentNode;
use App\Tests\Api\ECampApiTestCase;

/**
 * Base LIST (get) test case to be used for various ContentNode types.
 *
 * This test class covers all tests that are the same across all content node implementations
 *
 * @internal
 */
abstract class ListContentNodeTestCase extends ECampApiTestCase {
    // content nodes visible for user 1, 2, 3
    protected array $contentNodesCamp1and2 = [];

    // content nodes visislb for user 4
    protected array $contentNodesCampUnrelated = [];

    // content nodes visible for everyone
    protected array $contentNodesCampPrototypes = [];

    public function testListForAnonymousUser() {
        static::createBasicClient()->request('GET', $this->endpoint);
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testListForInvitedCollaborator() {
        $response = $this->list(user: static::$fixtures['user6invited']);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContainsItems($response, $this->contentNodesCampPrototypes);
    }

    public function testListForInactiveCollaborator() {
        $response = $this->list(user: static::$fixtures['user5inactive']);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContainsItems($response, $this->contentNodesCampPrototypes);
    }

    public function testListForUnrelatedUser() {
        $response = $this->list(user: static::$fixtures['user4unrelated']);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContainsItems($response, array_merge($this->contentNodesCampUnrelated, $this->contentNodesCampPrototypes));
    }

    public function testListForGuest() {
        $response = $this->list(user: static::$fixtures['user3guest']);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContainsItems($response, array_merge($this->contentNodesCamp1and2, $this->contentNodesCampPrototypes));
    }

    public function testListForMember() {
        $response = $this->list(user: static::$fixtures['user2member']);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContainsItems($response, array_merge($this->contentNodesCamp1and2, $this->contentNodesCampPrototypes));
    }

    public function testListForManager() {
        $response = $this->list(user: static::$fixtures['user1manager']);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContainsItems($response, array_merge($this->contentNodesCamp1and2, $this->contentNodesCampPrototypes));
    }
}
