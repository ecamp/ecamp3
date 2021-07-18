<?php

namespace App\Tests\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\DataPersister\ContentNodeDataPersister;
use App\Entity\ContentNode;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ContentNodeDataPersisterTest extends TestCase {
    private ContentNodeDataPersister $dataPersister;
    private MockObject | ContextAwareDataPersisterInterface $decoratedMock;
    private ContentNode $contentNode;

    protected function setUp(): void {
        $this->decoratedMock = $this->createMock(ContextAwareDataPersisterInterface::class);
        $this->contentNode = new ContentNode();

        $this->dataPersister = new ContentNodeDataPersister($this->decoratedMock);
    }

    public function testDelegatesSupportCheckToDecorated() {
        $this->decoratedMock
            ->expects($this->exactly(2))
            ->method('supports')
            ->willReturnOnConsecutiveCalls(true, false)
        ;

        $this->assertTrue($this->dataPersister->supports($this->contentNode, []));
        $this->assertFalse($this->dataPersister->supports($this->contentNode, []));
    }

    public function testDoesNotSupportNonContentNode() {
        $this->decoratedMock
            ->method('supports')
            ->willReturn(true)
        ;

        $this->assertFalse($this->dataPersister->supports([], []));
    }

    public function testDelegatesPersistToDecorated() {
        // given
        $this->decoratedMock->expects($this->once())
            ->method('persist')
        ;

        // when
        $this->dataPersister->persist($this->contentNode, []);

        // then
    }

    public function testSetsRootFromParentOnCreate() {
        // given
        $root = $this->createMock(ContentNode::class);
        $this->contentNode->parent = new ContentNode();
        $this->contentNode->parent->root = $root;
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var ContentNode $data */
        $data = $this->dataPersister->persist($this->contentNode, ['collection_operation_name' => 'post']);

        // then
        $this->assertEquals($root, $data->root);
    }

    public function testDoesNotSetRootFromParentOnUpdate() {
        // given
        $root = $this->createMock(ContentNode::class);
        $this->contentNode->parent = new ContentNode();
        $this->contentNode->parent->root = $root;
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var ContentNode $data */
        $data = $this->dataPersister->persist($this->contentNode, ['item_operation_name' => 'patch']);

        // then
        $this->assertNotEquals($root, $data->root);
    }
}
