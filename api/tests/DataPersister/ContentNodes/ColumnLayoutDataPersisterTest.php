<?php

namespace App\Tests\DataPersister\ContentNodes;

use App\DataPersister\ContentNode\ColumnLayoutDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\ContentNode\ColumnLayout;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ColumnLayoutDataPersisterTest extends TestCase {
    private ColumnLayoutDataPersister $dataPersister;
    private MockObject|DataPersisterObservable $dataPersisterObservable;
    private ColumnLayout $root;
    private ColumnLayout $contentNode;

    protected function setUp(): void {
        $this->dataPersisterObservable = $this->createMock(DataPersisterObservable::class);

        $this->contentNode = new ColumnLayout();

        $this->root = new ColumnLayout();
        $this->contentNode->parent = new ColumnLayout();
        $this->contentNode->parent->root = $this->root;

        $this->dataPersister = new ColumnLayoutDataPersister($this->dataPersisterObservable);
    }

    public function testDoesNotSupportNonColumnLayout() {
        $this->dataPersisterObservable
            ->method('supports')
            ->willReturn(true)
        ;

        $this->assertFalse($this->dataPersister->supports([], []));
    }

    public function testSetsRootFromParentOnCreate() {
        // when
        /** @var ColumnLayout $data */
        $data = $this->dataPersister->beforeCreate($this->contentNode);

        // then
        $this->assertEquals($this->root, $data->root);
    }

    public function testDoesNotSetRootFromParentOnUpdate() {
        // when
        /** @var ColumnLayout $data */
        $data = $this->dataPersister->beforeUpdate($this->contentNode);

        // then
        $this->assertNotEquals($this->root, $data->root);
    }
}
