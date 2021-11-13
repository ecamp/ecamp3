<?php

namespace App\Tests\DataPersister\ContentNodes;

use App\DataPersister\ContentNode\MaterialNodeDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentNode\MaterialNode;
use App\Entity\ContentType;
use App\Entity\MaterialItem;
use App\Entity\MaterialList;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class MaterialNodeDataPersisterTest extends TestCase {
    private MaterialNodeDataPersister $dataPersister;
    private MockObject|DataPersisterObservable $dataPersisterObservable;
    private MaterialNode $contentNode;

    protected function setUp(): void {
        $this->dataPersisterObservable = $this->createMock(DataPersisterObservable::class);
        $this->contentNode = new MaterialNode();

        $this->root = $this->createMock(ColumnLayout::class);
        $this->contentNode->parent = new ColumnLayout();
        $this->contentNode->parent->root = $this->root;

        $prototype = new MaterialNode();
        $prototype->instanceName = 'instance';
        $prototype->slot = 'left';
        $prototype->position = 99;
        $prototype->contentType = new ContentType();
        $prototype->contentType->name = 'test';
        $this->contentNode->prototype = $prototype;

        $materialList = new MaterialList();

        $materialItem = new MaterialItem();
        $materialItem->article = 'Milk';
        $materialItem->unit = 'liter';
        $materialItem->quantity = '5';
        $materialItem->materialList = $materialList;

        $prototype->addMaterialItem($materialItem);

        $this->dataPersister = new MaterialNodeDataPersister($this->dataPersisterObservable);
    }

    public function testDoesNotSupportNonMaterialNode() {
        $this->dataPersisterObservable
            ->method('supports')
            ->willReturn(true)
        ;

        $this->assertFalse($this->dataPersister->supports([], []));
    }

    public function testSetsRootFromParentOnCreate() {
        // when
        /** @var MaterialNode $data */
        $data = $this->dataPersister->beforeCreate($this->contentNode);

        // then
        $this->assertEquals($this->root, $data->root);
    }

    public function testCopyMaterialItemsFromPrototypeOnCreate() {
        // when
        /** @var MaterialNode $data */
        $data = $this->dataPersister->beforeCreate($this->contentNode);

        // then
        $this->assertEquals($data->materialItems[0]->article, $this->contentNode->prototype->materialItems[0]->article);
        $this->assertEquals($data->materialItems[0]->quantity, $this->contentNode->prototype->materialItems[0]->quantity);
        $this->assertEquals($data->materialItems[0]->unit, $this->contentNode->prototype->materialItems[0]->unit);
        $this->assertEquals($data->materialItems[0]->materialList, $this->contentNode->prototype->materialItems[0]->materialList);

        $this->assertEquals($data->instanceName, $this->contentNode->prototype->instanceName);
        $this->assertEquals($data->slot, $this->contentNode->prototype->slot);
        $this->assertEquals($data->position, $this->contentNode->prototype->position);
        $this->assertEquals($data->contentType, $this->contentNode->prototype->contentType);
    }

    public function testDoesNotSetRootFromParentOnUpdate() {
        // when
        /** @var MaterialNode $data */
        $data = $this->dataPersister->beforeUpdate($this->contentNode);

        // then
        $this->assertNotEquals($this->root, $data->root);
    }

    public function testDoesNotCopyMaterialItemsFromPrototypeOnUpdate() {
        // when
        /** @var MaterialNode $data */
        $data = $this->dataPersister->beforeUpdate($this->contentNode);

        // then
        $this->assertEquals(count($data->materialItems), 0);

        $this->assertNotEquals($data->instanceName, $this->contentNode->prototype->instanceName);
        $this->assertNotEquals($data->slot, $this->contentNode->prototype->slot);
        $this->assertNotEquals($data->position, $this->contentNode->prototype->position);
        $this->assertNotEquals($data->contentType, $this->contentNode->prototype->contentType);
    }
}
