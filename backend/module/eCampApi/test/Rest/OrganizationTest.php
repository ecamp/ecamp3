<?php

namespace eCamp\ApiTest\Rest;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\Organization;
use eCamp\Core\Entity\User;
use eCamp\CoreTest\Data\OrganizationTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class OrganizationTest extends AbstractApiControllerTestCase {
    /** @var Organization */
    protected $organization;

    /** @var User */
    protected $user;

    private $apiEndpoint = '/api/organizations';

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $organizationLoader = new OrganizationTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($organizationLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->organization = $organizationLoader->getReference(OrganizationTestData::$ORG1);

        $this->authenticateUser($this->user);
    }

    public function testFetch(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->organization->getId()}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "id": "{$this->organization->getId()}",
                "name": "Organization1"
            }
JSON;

        $expectedLinks = <<<JSON
            {
                "self": {
                    "href": "http://{$this->host}{$this->apiEndpoint}/{$this->organization->getId()}"
                }
            }
JSON;
        $expectedEmbeddedObjects = [];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testFetchAll(): void {
        $this->dispatch("{$this->apiEndpoint}?page_size=10", 'GET');

        $this->assertResponseStatusCode(200);

        $this->assertEquals(1, $this->getResponseContent()->total_items);
        $this->assertEquals(10, $this->getResponseContent()->page_size);
        $this->assertEquals("http://{$this->host}{$this->apiEndpoint}?page_size=10&page=1", $this->getResponseContent()->_links->self->href);
        $this->assertEquals($this->organization->getId(), $this->getResponseContent()->_embedded->items[0]->id);
    }

    public function testCreateForbidden(): void {
        $this->dispatch("{$this->apiEndpoint}", 'POST');
        $this->assertResponseStatusCode(405);
    }

    public function testPatchForbidden(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->organization->getId()}", 'PATCH');
        $this->assertResponseStatusCode(405);
    }

    public function testUpdateForbidden(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->organization->getId()}", 'PUT');
        $this->assertResponseStatusCode(405);
    }

    public function testDeleteForbidden(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->organization->getId()}", 'DELETE');
        $this->assertResponseStatusCode(405);
    }
}
