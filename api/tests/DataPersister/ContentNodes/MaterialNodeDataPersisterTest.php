<?php

namespace App\Tests\DataPersister\ContentNodes;

use App\DataPersister\ContentNode\MaterialNodeDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentNode\MaterialNode;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class MaterialNodeDataPersisterTest extends TestCase {
    private MaterialNodeDataPersister $dataPersister;
    private MockObject|DataPersisterObservable $dataPersisterObservable;
    private ColumnLayout $root;
    private MaterialNode $contentNode;

    protected function setUp(): void {
        $this->dataPersisterObservable = $this->createMock(DataPersisterObservable::class);
        $this->contentNode = new MaterialNode();

        $this->root = new ColumnLayout();
        $this->contentNode->parent = new ColumnLayout();
        $this->contentNode->parent->root = $this->root;

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

    public function testDoesNotSetRootFromParentOnUpdate() {
        // when
        /** @var MaterialNode $data */
        $data = $this->dataPersister->beforeUpdate($this->contentNode);

        // then
        $this->assertNotEquals($this->root, $data->root);
    }
}
