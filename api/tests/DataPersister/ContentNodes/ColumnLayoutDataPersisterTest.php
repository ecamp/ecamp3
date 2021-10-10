<?php

namespace App\Tests\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\DataPersister\ContentNode\ColumnLayoutDataPersister;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ColumnLayoutDataPersisterTest extends TestCase {
    private ColumnLayoutDataPersister $dataPersister;
    private MockObject|ContextAwareDataPersisterInterface $decoratedMock;
    private ColumnLayout $contentNode;

    protected function setUp(): void {
        $this->decoratedMock = $this->createMock(ContextAwareDataPersisterInterface::class);
        $this->contentNode = new ColumnLayout();

        $this->root = $this->createMock(ColumnLayout::class);
        $this->contentNode->parent = new ColumnLayout();
        $this->contentNode->parent->root = $this->root;

        $this->contentNode->prototype = new ColumnLayout();
        $this->contentNode->prototype->instanceName = 'instance';
        $this->contentNode->prototype->slot = 'left';
        $this->contentNode->prototype->position = 99;
        $this->contentNode->prototype->contentType = new ContentType();
        $this->contentNode->prototype->contentType->name = 'test';

        $this->dataPersister = new ColumnLayoutDataPersister($this->decoratedMock);
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

    public function testDoesNotSupportNonColumnLayout() {
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
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var ColumnLayout $data */
        $data = $this->dataPersister->persist($this->contentNode, ['collection_operation_name' => 'post']);

        // then
        $this->assertEquals($this->root, $data->root);
    }

    public function testCopyFromPrototypeOnCreate() {
        // given
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var ColumnLayout $data */
        $data = $this->dataPersister->persist($this->contentNode, ['collection_operation_name' => 'post']);

        // then
        $this->assertEquals($data->instanceName, $this->contentNode->prototype->instanceName);
        $this->assertEquals($data->slot, $this->contentNode->prototype->slot);
        $this->assertEquals($data->position, $this->contentNode->prototype->position);
        $this->assertEquals($data->contentType, $this->contentNode->prototype->contentType);
    }

    public function testDoesNotOverrideDataOnCreate() {
        // given
        $this->contentNode->instanceName = 'testInstance';
        $this->contentNode->slot = 'right';
        $this->contentNode->position = 51;
        $this->contentNode->contentType = new ContentType();

        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var ColumnLayout $data */
        $data = $this->dataPersister->persist($this->contentNode, ['collection_operation_name' => 'post']);

        // then
        $this->assertNotEquals($data->instanceName, $this->contentNode->prototype->instanceName);
        $this->assertNotEquals($data->slot, $this->contentNode->prototype->slot);
        $this->assertNotEquals($data->position, $this->contentNode->prototype->position);
        $this->assertNotEquals($data->contentType, $this->contentNode->prototype->contentType);
    }

    public function testDoesNotSetRootFromParentOnUpdate() {
        // given
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var ColumnLayout $data */
        $data = $this->dataPersister->persist($this->contentNode, ['item_operation_name' => 'patch']);

        // then
        $this->assertNotEquals($this->root, $data->root);
    }

    public function testDoesNotCopyDataFromPrototypeOnUpdate() {
        // given
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var ColumnLayout $data */
        $data = $this->dataPersister->persist($this->contentNode, ['item_operation_name' => 'patch']);

        // then
        $this->assertNotEquals($data->instanceName, $this->contentNode->prototype->instanceName);
        $this->assertNotEquals($data->slot, $this->contentNode->prototype->slot);
        $this->assertNotEquals($data->position, $this->contentNode->prototype->position);
        $this->assertNotEquals($data->contentType, $this->contentNode->prototype->contentType);
    }
}
