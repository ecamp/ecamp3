<?php

namespace App\Tests\Api\ContentNodes\MultiSelect\Option;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListMultiSelectOptionTest extends ECampApiTestCase {
    protected array $entitiesCamp1 = [];

    protected array $entitiesCampUnrelated = [];

    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/multi_select_options';

        $this->entitiesCamp1 = [
            $this->getIriFor('multiSelectOption1'),
            $this->getIriFor('multiSelectOption2'),
            $this->getIriFor('multiSelectOption3'),
        ];

        $this->entitiesCampUnrelated = [
            $this->getIriFor('multiSelectOptionCampUnrelated'),
        ];
    }

    public function testListMultiselectOptionsOrdersByPosition() {
        $client = static::createClientWithCredentials();
        $response = $client->request('GET', '/content_node/multi_select_options?multiSelect='.$this->getIriFor('multiSelect1'));
        $this->assertEquals([
            ['href' => $this->getIriFor('multiSelectOption2')],
            ['href' => $this->getIriFor('multiSelectOption1')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListOptionsFilteredByMultiSelect() {
        // when
        $response = static::createClientWithCredentials()->request('GET', "{$this->endpoint}?multiSelect=".$this->getIriFor('multiSelect1'));

        // then
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContainsItems($response, [
            $this->getIriFor('multiSelectOption1'),
            $this->getIriFor('multiSelectOption2'),
        ]);
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
