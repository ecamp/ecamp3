<?php

namespace App\Tests\Api\ContentNodes;

use App\Entity\ContentNode;
use App\Entity\User;
use App\Tests\Api\ECampApiTestCase;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Base LIST (get) test case to be used for various ContentNode types.
 *
 * This test class covers all tests that are the same across all content node implementations
 *
 * @internal
 */
abstract class ListContentNodeTestCase extends ECampApiTestCase {
    protected $defaultContentNode;

    protected $endpoint = '';

    protected array $contentNodesCamp1 = [];

    protected array $contentNodesCampUnrelated = [];

    public function testListForAnonymousUser() {
        static::createBasicClient()->request('GET', "/content_node/{$this->endpoint}");
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
        $this->assertJsonContainsItems($response, $this->contentNodesCampUnrelated);
    }

    public function testListForGuest() {
        $response = $this->list(user: static::$fixtures['user3guest']);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContainsItems($response, $this->contentNodesCamp1);
    }

    public function testListForMember() {
        $response = $this->list(user: static::$fixtures['user2member']);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContainsItems($response, $this->contentNodesCamp1);
    }

    public function testListForManager() {
        $response = $this->list(user: static::$fixtures['user1manager']);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContainsItems($response, $this->contentNodesCamp1);
    }

    protected function list(?User $user = null) {
        $credentials = null;
        if (null !== $user) {
            $credentials = ['username' => $user->getUsername()];
        }

        return static::createClientWithCredentials($credentials)->request('GET', "/content_node/{$this->endpoint}");
    }

    /**
     * @param ResponseInterface $response Response from API
     * @param array             $items    array of IRIs
     */
    protected function assertJsonContainsItems(ResponseInterface $response, array $items = []) {
        $this->assertJsonContains([
            'totalItems' => count($items),
        ]);

        // TODO: remove if once PR #2062 is merged
        if (!empty($items)) {
            $this->assertJsonContains([
                '_links' => [
                    'items' => [],
                ],
                '_embedded' => [
                    'items' => [],
                ],
            ]);

            $this->assertEqualsCanonicalizing(
                array_map(fn ($iri): array => ['href' => $iri], $items),
                $response->toArray()['_links']['items']
            );
        }
    }
}
