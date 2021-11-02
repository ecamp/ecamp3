<?php

namespace App\Tests\Api\ContentNodes\Storyboard\Section;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListStoryboardSectionTest extends ECampApiTestCase {
    protected array $entitiesCamp1 = [];

    protected array $entitiesCampUnrelated = [];

    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/storyboard_sections';

        $this->entitiesCamp1 = [
            $this->getIriFor('storyboardSection1'),
            $this->getIriFor('storyboardSection2'),
        ];

        $this->entitiesCampUnrelated = [
            $this->getIriFor('storyboardSectionCampUnrelated'),
        ];
    }

    public function testListSectionsFilteredByStoryboard() {
        // when
        $response = static::createClientWithCredentials()->request('GET', "{$this->endpoint}?storyboard=".$this->getIriFor('storyboard1'));

        // then
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContainsItems($response, [$this->getIriFor('storyboardSection1')]);
    }

    /**
     * Standard security checks.
     */
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
        $this->assertJsonContainsItems($response, []);
    }

    public function testListForInactiveCollaborator() {
        $response = $this->list(user: static::$fixtures['user5inactive']);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContainsItems($response, []);
    }

    public function testListForUnrelatedUser() {
        $response = $this->list(user: static::$fixtures['user4unrelated']);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContainsItems($response, $this->entitiesCampUnrelated);
    }

    public function testListForGuest() {
        $response = $this->list(user: static::$fixtures['user3guest']);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContainsItems($response, $this->entitiesCamp1);
    }

    public function testListForMember() {
        $response = $this->list(user: static::$fixtures['user2member']);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContainsItems($response, $this->entitiesCamp1);
    }

    public function testListForManager() {
        $response = $this->list(user: static::$fixtures['user1manager']);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContainsItems($response, $this->entitiesCamp1);
    }
}
