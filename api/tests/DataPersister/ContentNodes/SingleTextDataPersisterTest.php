<?php

namespace App\Tests\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\DataPersister\ContentNode\SingleTextDataPersister;
use App\Entity\ContentNode\SingleText;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class SingleTextDataPersisterTest extends TestCase {
    private SingleTextDataPersister $dataPersister;
    private MockObject|ContextAwareDataPersisterInterface $decoratedMock;
    private SingleText $contentNode;

    protected function setUp(): void {
        $this->decoratedMock = $this->createMock(ContextAwareDataPersisterInterface::class);
        $this->contentNode = new SingleText();

        $this->root = $this->createMock(SingleText::class);
        $this->contentNode->parent = new SingleText();
        $this->contentNode->parent->root = $this->root;

        $this->contentNode->prototype = new SingleText();
        $this->contentNode->prototype->text = 'Test';

        $this->dataPersister = new SingleTextDataPersister($this->decoratedMock);
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

    public function testDoesNotSupportNonSingleText() {
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
        /** @var SingleText $data */
        $data = $this->dataPersister->persist($this->contentNode, ['collection_operation_name' => 'post']);

        // then
        $this->assertEquals($this->root, $data->root);
    }

    public function testCopyTextFromPrototypeOnCreate() {
        // given
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var SingleText $data */
        $data = $this->dataPersister->persist($this->contentNode, ['collection_operation_name' => 'post']);

        // then
        $this->assertEquals($data->text, $this->contentNode->prototype->text);
    }

    public function testDoesNotOverrideTextOnCreate() {
        // given
        $this->contentNode->text = 'test111';
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var SingleText $data */
        $data = $this->dataPersister->persist($this->contentNode, ['collection_operation_name' => 'post']);

        // then
        $this->assertNotEquals($data->text, $this->contentNode->prototype->text);
        $this->assertEquals($data->text, 'test111');
    }

    public function testDoesNotSetRootFromParentOnUpdate() {
        // given
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var SingleText $data */
        $data = $this->dataPersister->persist($this->contentNode, ['item_operation_name' => 'patch']);

        // then
        $this->assertNotEquals($this->root, $data->root);
    }

    public function testDoesNotCopyTextFromPrototypeOnUpdate() {
        // given
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var SingleText $data */
        $data = $this->dataPersister->persist($this->contentNode, ['item_operation_name' => 'patch']);

        // then
        $this->assertNotEquals($data->text, $this->contentNode->prototype->text);
    }
}
