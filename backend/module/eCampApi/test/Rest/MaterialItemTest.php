<?php

namespace eCamp\ApiTest\Rest;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\CoreTest\Data\MaterialItemTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class MaterialItemTest extends AbstractApiControllerTestCase {
    /** @var MaterialItem */
    protected $materialItem;

    /** @var User */
    protected $user;

    private $apiEndpoint = '/api/material-items';

    public function setUp() {
        parent::setUp();

        $userLoader = new UserTestData();
        $materialItemLoader = new MaterialItemTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($materialItemLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->materialItem = $materialItemLoader->getReference(MaterialItemTestData::$MATERIALITEM1);

        $this->authenticateUser($this->user);
    }

    public function testFetch() {
        $this->dispatch("{$this->apiEndpoint}/{$this->materialItem->getId()}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "id": "{$this->materialItem->getId()}",
                "quantity": 2,
                "unit": "kg",
                "article": "art",
                "period": null,
                "activityContent": null
            }
JSON;

        $expectedLinks = <<<JSON
            {
                "self": {
                    "href": "http://{$this->host}{$this->apiEndpoint}/{$this->materialItem->getId()}"
                }
            }
JSON;
        $expectedEmbeddedObjects = ['materialList'];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }
}
