<?php

namespace App\Tests\State\ContentNodes;

use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\ContentNode\ColumnLayout;
use App\State\ContentNode\ContentNodePersistProcessor;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ContentNodePersistProcessorTest extends TestCase {
    private ContentNodePersistProcessor $processor;
    private ColumnLayout $root;
    private ColumnLayout $contentNode;

    protected function setUp(): void {
        $decoratedProcessor = $this->createMock(ProcessorInterface::class);

        $this->contentNode = new ColumnLayout();

        $this->root = new ColumnLayout();
        $this->contentNode->parent = new ColumnLayout();
        $this->contentNode->parent->root = $this->root;

        $this->processor = new ContentNodePersistProcessor($decoratedProcessor);
    }

    public function testSetsRootFromParentOnCreate() {
        // when
        /** @var ColumnLayout $data */
        $data = $this->processor->onBefore($this->contentNode, new Post());

        // then
        $this->assertEquals($this->root, $data->root);
    }

    public function testDoesNotSetRootFromParentOnUpdate() {
        // when
        /** @var ColumnLayout $data */
        $data = $this->processor->onBefore($this->contentNode, new Patch());

        // then
        $this->assertNotEquals($this->root, $data->root);
    }
}
