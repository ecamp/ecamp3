<?php

namespace eCamp\ApiTest\Rest;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\User;
use eCamp\CoreTest\Data\CategoryContentTypeTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class CategoryContentTypeTest extends AbstractApiControllerTestCase {
    /** @var CategoryContentType */
    protected $categoryContentType;

    /** @var User */
    protected $user;

    private $apiEndpoint = '/api/category-content-types';

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $categoryContentTypeLoader = new CategoryContentTypeTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($categoryContentTypeLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->categoryContentType = $categoryContentTypeLoader->getReference(CategoryContentTypeTestData::$CATEGORY_CONTENT_TYPE1);

        $this->authenticateUser($this->user);
    }

    public function testFetch(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->categoryContentType->getId()}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "id": "{$this->categoryContentType->getId()}"
            }
JSON;

        $expectedLinks = <<<JSON
            {
                "self": {
                    "href": "http://{$this->host}{$this->apiEndpoint}/{$this->categoryContentType->getId()}"
                }
            }
JSON;
        $expectedEmbeddedObjects = ['contentType'];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }
}
