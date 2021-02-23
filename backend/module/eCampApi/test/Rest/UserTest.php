<?php

namespace eCamp\ApiTest\Rest;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\User;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class UserTest extends AbstractApiControllerTestCase {
    /** @var User */
    protected $user;

    private $apiEndpoint = '/api/users';

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $loader = new Loader();
        $loader->addFixture($userLoader);

        $this->loadFixtures($loader);
        $this->user = $userLoader->getReference(UserTestData::$USER1);

        $this->authenticateUser($this->user);
    }

    public function testFetch(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->user->getId()}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "id": "{$this->user->getId()}",
                "username": "test-user",
                "displayName": "test-user",
                "mail": "test@ecamp3.dev",
                "relation": "me",
                "role": "user"
            }
JSON;

        $expectedLinks = <<<JSON
            {
                "self": {
                    "href": "http://{$this->host}{$this->apiEndpoint}/{$this->user->getId()}"
                }
            }
JSON;
        $expectedEmbeddedObjects = [];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testFetchAll(): void {
        $this->dispatch("{$this->apiEndpoint}?page_size=10", 'GET');

        $this->assertResponseStatusCode(200);

        $this->assertEquals(2, $this->getResponseContent()->total_items);
        $this->assertEquals(10, $this->getResponseContent()->page_size);
        $this->assertEquals("http://{$this->host}{$this->apiEndpoint}?page_size=10&page=1", $this->getResponseContent()->_links->self->href);
        $this->assertEquals($this->user->getId(), $this->getResponseContent()->_embedded->items[0]->id);
    }

    // TODO: more tests for user creation + registration necessary to cover all variations
    public function testCreateSuccess(): void {
        $this->setRequestContent([
            'state' => 'registered',
            'mailAddress' => 'test@test.com',
            'username' => 'test-user2', ]);

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        $this->assertResponseStatusCode(201);
        $this->assertEquals('test-user2', $this->getResponseContent()->username);
        $this->assertEquals('user', $this->getResponseContent()->role);
        $this->assertEquals('unrelated', $this->getResponseContent()->relation);
    }

    public function testUpdateSuccess(): void {
        $this->setRequestContent([
            'username' => 'test-user5', ]);

        $this->dispatch("{$this->apiEndpoint}/{$this->user->getId()}", 'PATCH');

        $this->assertResponseStatusCode(200);

        $this->assertEquals('test-user5', $this->getResponseContent()->username);
    }

    public function testDelete(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->user->getId()}", 'DELETE');

        $this->assertResponseStatusCode(204);

        $result = $this->getEntityManager()->find(User::class, $this->user->getId());
        $this->assertNull($result);
    }
}
