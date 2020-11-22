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

    public function setUp() {
        parent::setUp();

        $userLoader = new UserTestData();
        $loader = new Loader();
        $loader->addFixture($userLoader);

        $this->loadFixtures($loader);
        $this->user = $userLoader->getReference(UserTestData::$USER1);

        $this->authenticateUser($this->user);
    }

    public function testFetch() {
        $this->dispatch("{$this->apiEndpoint}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<'JSON'
            {
                "username": "test-user",
                "firstname": null,
                "surname": null,
                "nickname": null,
                "displayName": "test-user",
                "mail": "test@ecamp3.dev",
                "role": "user",
                "language": null,
                "birthday": null,
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

    public function testCreateSuccess() {
        $this->dispatch("{$this->apiEndpoint}", 'POST');

        $this->assertResponseStatusCode(405);
    }

    public function testUpdateSuccess() {
        $this->setRequestContent([
            'username' => 'test-user5',
            'firstname' => 'firstname',
            'surname' => 'surname',
            'nickname' => 'nickname',
            'language' => 'EN',
            'birthday' => '1990-07-01',
        ]);

        $this->dispatch("{$this->apiEndpoint}", 'PATCH');

        $this->assertResponseStatusCode(200);

        $this->assertEquals('test-user', $this->getResponseContent()->username); // username cannot be modified

        $this->assertEquals('firstname', $this->getResponseContent()->firstname);
        $this->assertEquals('surname', $this->getResponseContent()->surname);
        $this->assertEquals('nickname', $this->getResponseContent()->nickname);
        $this->assertEquals('EN', $this->getResponseContent()->language);
        $this->assertEquals('1990-07-01', $this->getResponseContent()->birthday);
    }

    public function testDelete() {
        $this->dispatch("{$this->apiEndpoint}", 'DELETE');

        $this->assertResponseStatusCode(405);
    }
}
