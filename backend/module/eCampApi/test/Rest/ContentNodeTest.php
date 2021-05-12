<?php

namespace eCamp\ApiTest\Rest;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\ContentNode;
use eCamp\Core\Entity\User;
use eCamp\CoreTest\Data\ContentNodeTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class ContentNodeTest extends AbstractApiControllerTestCase {
    /** @var ContentNode */
    protected $contentNode;
    /** @var ContentNode */
    protected $contentNode1Camp2;

    /** @var User */
    protected $user;

    private $apiEndpoint = '/api/content-nodes';

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $contentNodeLoader = new ContentNodeTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($contentNodeLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->contentNode = $contentNodeLoader->getReference(ContentNodeTestData::$CATEGORY_CONTENT1);
        $this->contentNode1Camp2 = $contentNodeLoader->getReference(ContentNodeTestData::$CATEGORY_CONTENT1_CAMP2);

        $this->authenticateUser($this->user);
    }

    public function testFetch(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->contentNode->getId()}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "id": "{$this->contentNode->getId()}",
                "instanceName": "Programm",
                "slot": null,
                "position": null,
                "contentTypeName": "Storyboard",
                "parent": null,
                "jsonConfig": null
            }
JSON;

        $expectedLinks = <<<JSON
            {
                "self": {
                    "href": "http://{$this->host}{$this->apiEndpoint}/{$this->contentNode->getId()}"
                },
                "owner": {
                    "href": "http://{$this->host}/api/categories/{$this->contentNode->getOwner()->getId()}"
                },
                "ownerCategory": {
                    "href": "http://{$this->host}/api/categories/{$this->contentNode->getOwner()->getId()}"
                },
                "children": {
                    "href": "http://{$this->host}/api/content-nodes?parentId={$this->contentNode->getId()}"
                },
                "sections": {
                    "href": "http://{$this->host}/api/content-type/storyboards?contentNodeId={$this->contentNode->getId()}"
                }
            }
JSON;

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, []);
    }

    public function testFetchAll(): void {
        $this->dispatch("{$this->apiEndpoint}?page_size=10", 'GET');

        $this->assertResponseStatusCode(200);

        $this->assertEquals(2, $this->getResponseContent()->total_items);
        $this->assertEquals(10, $this->getResponseContent()->page_size);
        $this->assertEquals("http://{$this->host}{$this->apiEndpoint}?page_size=10&page=1", $this->getResponseContent()->_links->self->href);
        $this->assertEquals($this->contentNode->getId(), $this->getResponseContent()->_embedded->items[0]->id);
    }

    public function testCreateRootNode(): void {
        $this->setRequestContent([
            'ownerId' => $this->contentNode->getOwner()->getId(),
            'contentTypeId' => $this->contentNode->getContentType()->getId(),
        ]);
        $this->dispatch("{$this->apiEndpoint}", 'POST');
        $this->assertResponseStatusCode(201);
    }

    public function testCreateChildNode(): void {
        $this->setRequestContent([
            'parentId' => $this->contentNode->getId(),
            'contentTypeId' => $this->contentNode->getContentType()->getId(),
            'slot' => '1',
            'position' => 20,
        ]);
        $this->dispatch("{$this->apiEndpoint}", 'POST');
        $this->assertResponseStatusCode(201);

        $this->assertEquals(20, $this->getResponseContent()->position);
    }

    public function testCreateChildNodeAutomaticallySetsPositionWhenNotPassed(): void {
        $this->setRequestContent([
            'parentId' => $this->contentNode->getId(),
            'contentTypeId' => $this->contentNode->getContentType()->getId(),
            'slot' => '1',
        ]);
        $this->dispatch("{$this->apiEndpoint}", 'POST');
        $this->assertResponseStatusCode(201);

        $this->assertEquals(1, $this->getResponseContent()->position);
    }

    public function testPatch(): void {
        $this->setRequestContent([
            'jsonConfig' => null,
        ]);

        $this->dispatch("{$this->apiEndpoint}/{$this->contentNode->getId()}", 'PATCH');
        $this->assertResponseStatusCode(200);

        $this->assertObjectHasAttribute('jsonConfig', $this->getResponseContent());
        $this->assertEquals(null, $this->getResponseContent()->jsonConfig);
    }

    public function testPatchMovingContentNodeToADifferentCampYieldsValidationError(): void {
        $this->setRequestContent([
            'parentId' => $this->contentNode1Camp2->getId(),
        ]);

        $this->dispatch("{$this->apiEndpoint}/{$this->contentNode->getId()}", 'PATCH');
        $this->assertResponseStatusCode(422);

        $this->assertObjectHasAttribute('validation_messages', $this->getResponseContent());
        $this->assertObjectHasAttribute('parentId', $this->getResponseContent()->validation_messages);
        $this->assertObjectHasAttribute('notSameCamp', $this->getResponseContent()->validation_messages->parentId);
        $this->assertEquals('Moving ContentNodes across camps is not implemented. Trying to move from camp ' . $this->contentNode->getCamp()->getId() . ' to camp ' . $this->contentNode1Camp2->getCamp()->getId(), $this->getResponseContent()->validation_messages->parentId->notSameCamp);
    }

    public function testDeleteForbidden(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->contentNode->getId()}", 'DELETE');
        $this->assertResponseStatusCode(204);
    }
}
