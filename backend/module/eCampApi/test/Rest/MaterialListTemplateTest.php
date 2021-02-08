<?php

namespace eCamp\ApiTest\Rest;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\MaterialListTemplate;
use eCamp\CoreTest\Data\CampTemplateTestData;
use eCamp\CoreTest\Data\MaterialListTemplateTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class MaterialListTemplateTest extends AbstractApiControllerTestCase {
    /** @var MaterialListTemplate */
    protected $materialListTemplate;

    /** @var CampTemplate */
    protected $campTemplate;

    /** @var User */
    protected $user;

    private $apiEndpoint = '/api/material-list-templates';

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $campTemplateLoader = new CampTemplateTestData();
        $materialListTemplateLoader = new MaterialListTemplateTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($campTemplateLoader);
        $loader->addFixture($materialListTemplateLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->campTemplate = $campTemplateLoader->getReference(CampTemplateTestData::$TEMPLATE1);
        $this->materialListTemplate = $materialListTemplateLoader->getReference(MaterialListTemplateTestData::$MATERIALLISTTEMPLATE1);

        $this->authenticateUser($this->user);
    }

    public function testFetch(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->materialListTemplate->getId()}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "id": "{$this->materialListTemplate->getId()}",
                "name": "MaterialListTemplate1"
            }
JSON;

        $expectedLinks = <<<JSON
            {
                "self": {
                    "href": "http://{$this->host}{$this->apiEndpoint}/{$this->materialListTemplate->getId()}"
                }
            }
JSON;
        $expectedEmbeddedObjects = [];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testFetchAll(): void {
        $campTemplateId = $this->campTemplate->getId();
        $this->dispatch("{$this->apiEndpoint}?page_size=10&campTemplateId={$campTemplateId}", 'GET');

        $this->assertResponseStatusCode(200);

        $this->assertEquals(1, $this->getResponseContent()->total_items);
        $this->assertEquals(10, $this->getResponseContent()->page_size);
        $this->assertEquals("http://{$this->host}{$this->apiEndpoint}?page_size=10&campTemplateId={$campTemplateId}&page=1", $this->getResponseContent()->_links->self->href);
        $this->assertEquals($this->materialListTemplate->getId(), $this->getResponseContent()->_embedded->items[0]->id);
    }

    public function testCreateForbidden(): void {
        $this->dispatch("{$this->apiEndpoint}", 'POST');
        $this->assertResponseStatusCode(405);
    }

    public function testPatchForbidden(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->materialListTemplate->getId()}", 'PATCH');
        $this->assertResponseStatusCode(405);
    }

    public function testUpdateForbidden(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->materialListTemplate->getId()}", 'PUT');
        $this->assertResponseStatusCode(405);
    }

    public function testDeleteForbidden(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->materialListTemplate->getId()}", 'DELETE');
        $this->assertResponseStatusCode(405);
    }
}
