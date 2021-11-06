<?php

namespace App\Tests\DataPersister\ContentNodes;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\DataPersister\ContentNode\MultiSelectDataPersister;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentNode\MultiSelect;
use App\Entity\ContentNode\MultiSelectOption;
use App\Entity\ContentType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class MultiSelectDataPersisterTest extends TestCase {
    private MultiSelectDataPersister $dataPersister;
    private MockObject|ContextAwareDataPersisterInterface $decoratedMock;
    private MultiSelect $contentNode;

    protected function setUp(): void {
        $this->decoratedMock = $this->createMock(ContextAwareDataPersisterInterface::class);
        $this->contentNode = new MultiSelect();

        $this->root = $this->createMock(ColumnLayout::class);
        $this->contentNode->parent = new ColumnLayout();
        $this->contentNode->parent->root = $this->root;

        $contentType = new ContentType();
        $contentType->jsonConfig = [
            'items' => [
                'key1',
                'key2',
            ],
        ];
        $this->contentNode->contentType = $contentType;

        $this->dataPersister = new MultiSelectDataPersister($this->decoratedMock);
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

    public function testDoesNotSupportNonMultiSelect() {
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
        /** @var MultiSelect $data */
        $data = $this->dataPersister->persist($this->contentNode, ['collection_operation_name' => 'post']);

        // then
        $this->assertEquals($this->root, $data->root);
    }

    public function testCopyMultiSelectOptionsFromContentTypeOnCreate() {
        // given
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var MultiSelect $data */
        $data = $this->dataPersister->persist($this->contentNode, ['collection_operation_name' => 'post']);

        // then
        $this->assertEquals($data->options[0]->translateKey, 'key1');
        $this->assertEquals($data->options[0]->checked, false);
        $this->assertEquals($data->options[0]->getPos(), 0);

        $this->assertEquals($data->options[1]->translateKey, 'key2');
        $this->assertEquals($data->options[1]->checked, false);
        $this->assertEquals($data->options[1]->getPos(), 1);
    }

    public function testCopyMultiSelectOptionsFromPrototypeOnCreate() {
        // given
        $this->addPrototype();
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var MultiSelect $data */
        $data = $this->dataPersister->persist($this->contentNode, ['collection_operation_name' => 'post']);

        // then
        $this->assertEquals($data->options[0]->translateKey, $this->contentNode->prototype->options[0]->translateKey);
        $this->assertEquals($data->options[0]->checked, $this->contentNode->prototype->options[0]->checked);
        $this->assertEquals($data->options[0]->getPos(), $this->contentNode->prototype->options[0]->getPos());
    }

    public function testDoesNotSetRootFromParentOnUpdate() {
        // given
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var MultiSelect $data */
        $data = $this->dataPersister->persist($this->contentNode, ['item_operation_name' => 'patch']);

        // then
        $this->assertNotEquals($this->root, $data->root);
    }

    public function testDoesNotCopyMultiSelectOptionsOnUpdate() {
        // given
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var MultiSelect $data */
        $data = $this->dataPersister->persist($this->contentNode, ['item_operation_name' => 'patch']);

        // then
        $this->assertEquals(count($data->options), 0);
    }

    private function addPrototype() {
        $prototype = new MultiSelect();

        $option = new MultiSelectOption();
        $option->translateKey = 'translateKey';
        $option->checked = true;
        $option->setPos(51);

        $prototype->addOption($option);

        $this->contentNode->prototype = $prototype;
    }
}
