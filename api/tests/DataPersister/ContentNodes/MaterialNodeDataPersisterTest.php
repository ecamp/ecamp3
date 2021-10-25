<?php

namespace App\Tests\DataPersister\ContentNodes;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\DataPersister\ContentNode\MaterialNodeDataPersister;
use App\Entity\ContentNode\MaterialNode;
use App\Entity\MaterialItem;
use App\Entity\MaterialList;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class MaterialNodeDataPersisterTest extends TestCase {
    private MaterialNodeDataPersister $dataPersister;
    private MockObject|ContextAwareDataPersisterInterface $decoratedMock;
    private MaterialNode $contentNode;

    protected function setUp(): void {
        $this->decoratedMock = $this->createMock(ContextAwareDataPersisterInterface::class);
        $this->contentNode = new MaterialNode();

        $this->root = $this->createMock(MaterialNode::class);
        $this->contentNode->parent = new MaterialNode();
        $this->contentNode->parent->root = $this->root;

        $prototype = new MaterialNode();
        $this->contentNode->prototype = $prototype;

        $materialList = new MaterialList();

        $materialItem = new MaterialItem();
        $materialItem->article = 'Milk';
        $materialItem->unit = 'liter';
        $materialItem->quantity = '5';
        $materialItem->materialList = $materialList;

        $prototype->addMaterialItem($materialItem);

        $this->dataPersister = new MaterialNodeDataPersister($this->decoratedMock);
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

    public function testDoesNotSupportNonMaterialNode() {
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
        /** @var MaterialNode $data */
        $data = $this->dataPersister->persist($this->contentNode, ['collection_operation_name' => 'post']);

        // then
        $this->assertEquals($this->root, $data->root);
    }

    public function testCopyMaterialItemsFromPrototypeOnCreate() {
        // given
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var MaterialNode $data */
        $data = $this->dataPersister->persist($this->contentNode, ['collection_operation_name' => 'post']);

        // then
        $this->assertEquals($data->materialItems[0]->article, $this->contentNode->prototype->materialItems[0]->article);
        $this->assertEquals($data->materialItems[0]->quantity, $this->contentNode->prototype->materialItems[0]->quantity);
        $this->assertEquals($data->materialItems[0]->unit, $this->contentNode->prototype->materialItems[0]->unit);
        $this->assertEquals($data->materialItems[0]->materialList, $this->contentNode->prototype->materialItems[0]->materialList);
    }

    public function testDoesNotSetRootFromParentOnUpdate() {
        // given
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var MaterialNode $data */
        $data = $this->dataPersister->persist($this->contentNode, ['item_operation_name' => 'patch']);

        // then
        $this->assertNotEquals($this->root, $data->root);
    }

    public function testDoesNotCopyMaterialItemsFromPrototypeOnUpdate() {
        // given
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var MaterialNode $data */
        $data = $this->dataPersister->persist($this->contentNode, ['item_operation_name' => 'patch']);

        // then
        $this->assertEquals(count($data->materialItems), 0);
    }
}
