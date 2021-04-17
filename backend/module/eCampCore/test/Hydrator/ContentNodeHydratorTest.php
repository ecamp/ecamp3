<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\ContentType\ContentTypeStrategyInterface;
use eCamp\Core\ContentType\ContentTypeStrategyProvider;
use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\Category;
use eCamp\Core\Entity\ContentNode;
use eCamp\Core\Entity\ContentType;
use eCamp\Core\Hydrator\ContentNodeHydrator;
use eCamp\LibTest\PHPUnit\AbstractTestCase;
use Laminas\ServiceManager\ServiceManager;

/**
 * @internal
 */
class ContentNodeHydratorTest extends AbstractTestCase {
    public function testExtract(): void {
        $contentType = new ContentType();
        $contentType->setName('type-name');
        $contentType->setStrategyClass(DummyContentTypeStrategy::class);

        $contentNodeParent = new ContentNode();
        $contentNodeParent->setContentType($contentType);
        $contentNodeParent->setOwner(new Category());

        $contentNode = new ContentNode();
        $contentNode->setInstanceName('my-name');
        $contentNode->setSlot('my-slot');
        $contentNode->setPosition(2);
        $contentNode->setContentType($contentType);
        $contentNode->setOwner(new Activity());
        $contentNode->setParent($contentNodeParent);

        $container = new ServiceManager();
        $contentTypeStrategyProvider = new ContentTypeStrategyProvider($container);
        $hydrator = new ContentNodeHydrator($contentTypeStrategyProvider);
        $data = $hydrator->extract($contentNode);

        $this->assertEquals('my-name', $data['instanceName']);
        $this->assertEquals('my-slot', $data['slot']);
        $this->assertEquals(2, $data['position']);
        $this->assertEquals('type-name', $data['contentTypeName']);
        $this->assertNotNull($data['parent']);
        $this->assertNotNull($data['contentType']);
        $this->assertNotNull($data['owner']);
        $this->assertEquals('test-data', $data['strategy-data']);

        $data = $hydrator->extract($contentNodeParent);
        $this->assertNull($data['parent']);
        $this->assertNotNull($data['owner']);
    }

    public function testHydrate(): void {
        $contentNode = new ContentNode();
        $data = [
            'instanceName' => 'my-name',
            'slot' => 'top',
            'position' => 3,
        ];

        $container = new ServiceManager();
        $contentTypeStrategyProvider = new ContentTypeStrategyProvider($container);
        $hydrator = new ContentNodeHydrator($contentTypeStrategyProvider);
        $hydrator->hydrate($data, $contentNode);

        $this->assertEquals('my-name', $contentNode->getInstanceName());
        $this->assertEquals('top', $contentNode->getSlot());
        $this->assertEquals(3, $contentNode->getPosition());
    }
}

class DummyContentTypeStrategy implements ContentTypeStrategyInterface {
    public function contentNodeExtract(ContentNode $contentNode): array {
        return [
            'strategy-data' => 'test-data',
        ];
    }

    public function contentNodeCreated(ContentNode $contentNode): void {
    }

    public function validateContentNode(ContentNode $contentNode): void {
    }
}
