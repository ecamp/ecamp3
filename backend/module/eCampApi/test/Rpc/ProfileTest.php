<?php

namespace eCamp\ApiTest\Rest;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\User;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class ProfileTest extends AbstractApiControllerTestCase {
    /** @var User */
    protected $user;

    private $apiEndpoint = '/api/profile';

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
        $this->dispatch("{$this->apiEndpoint}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "id": "{$this->user->getId()}",
                "username": "test-user",
                "firstname": null,
                "surname": null,
                "nickname": null,
                "displayName": "test-user",
                "mail": "test@ecamp3.dev",
                "role": "user",
                "language": null,
                "isAdmin": false
            }
JSON;

        $expectedLinks = <<<JSON
            {
                "self": {
                    "href": "http://{$this->host}{$this->apiEndpoint}"
                }
            }
JSON;
        $expectedEmbeddedObjects = [];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testCreateSuccess(): void {
        $this->dispatch("{$this->apiEndpoint}", 'POST');

        $this->assertResponseStatusCode(405);
    }

    public function testUpdateSuccess(): void {
        $this->setRequestContent([
            'username' => 'test-user5',
            'firstname' => 'firstname',
            'surname' => 'surname',
            'nickname' => 'nickname',
            'language' => 'EN',
        ]);

        $this->dispatch("{$this->apiEndpoint}", 'PATCH');

        $this->assertResponseStatusCode(200);

        $this->assertEquals('test-user', $this->getResponseContent()->username); // username cannot be modified

        $this->assertEquals('firstname', $this->getResponseContent()->firstname);
        $this->assertEquals('surname', $this->getResponseContent()->surname);
        $this->assertEquals('nickname', $this->getResponseContent()->nickname);
        $this->assertEquals('EN', $this->getResponseContent()->language);
    }

    public function testDelete(): void {
        $this->dispatch("{$this->apiEndpoint}", 'DELETE');

        $this->assertResponseStatusCode(405);
    }
}
