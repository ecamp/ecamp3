<?php

namespace App\Tests\DataPersister\ContentNodes;

use App\DataPersister\ContentNode\ColumnLayoutDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ColumnLayoutDataPersisterTest extends TestCase {
    private ColumnLayoutDataPersister $dataPersister;
    private MockObject|DataPersisterObservable $dataPersisterObservable;
    private ColumnLayout $contentNode;

    protected function setUp(): void {
        $this->dataPersisterObservable = $this->createMock(DataPersisterObservable::class);

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

    public function testCopyFromPrototypeOnCreate() {
        // when
        /** @var ColumnLayout $data */
        $data = $this->dataPersister->beforeCreate($this->contentNode);

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

        // when
        /** @var ColumnLayout $data */
        $data = $this->dataPersister->beforeCreate($this->contentNode);

        // then
        $this->assertNotEquals($data->instanceName, $this->contentNode->prototype->instanceName);
        $this->assertNotEquals($data->slot, $this->contentNode->prototype->slot);
        $this->assertNotEquals($data->position, $this->contentNode->prototype->position);
        $this->assertNotEquals($data->contentType, $this->contentNode->prototype->contentType);
    }

    public function testDoesNotSetRootFromParentOnUpdate() {
        // when
        /** @var ColumnLayout $data */
        $data = $this->dataPersister->beforeUpdate($this->contentNode);

        // then
        $this->assertNotEquals($this->root, $data->root);
    }

    public function testDoesNotCopyDataFromPrototypeOnUpdate() {
        // when
        /** @var ColumnLayout $data */
        $data = $this->dataPersister->beforeUpdate($this->contentNode);

        // then
        $this->assertNotEquals($data->instanceName, $this->contentNode->prototype->instanceName);
        $this->assertNotEquals($data->slot, $this->contentNode->prototype->slot);
        $this->assertNotEquals($data->position, $this->contentNode->prototype->position);
        $this->assertNotEquals($data->contentType, $this->contentNode->prototype->contentType);
    }
}
