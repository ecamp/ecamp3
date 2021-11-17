<?php

namespace App\Tests\DataPersister\ContentNodes;

use App\DataPersister\ContentNode\SingleTextDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\ContentNode\SingleText;
use App\Entity\ContentType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class SingleTextDataPersisterTest extends TestCase {
    private SingleTextDataPersister $dataPersister;
    private MockObject|DataPersisterObservable $dataPersisterObservable;
    private SingleText $contentNode;

    protected function setUp(): void {
        $this->dataPersisterObservable = $this->createMock(DataPersisterObservable::class);
        $this->contentNode = new SingleText();

        $this->root = $this->createMock(SingleText::class);
        $this->contentNode->parent = new SingleText();
        $this->contentNode->parent->root = $this->root;

        $this->contentNode->prototype = new SingleText();
        $this->contentNode->prototype->text = 'Test';

        $this->contentNode->prototype->instanceName = 'instance';
        $this->contentNode->prototype->slot = 'left';
        $this->contentNode->prototype->position = 99;
        $this->contentNode->prototype->contentType = new ContentType();
        $this->contentNode->prototype->contentType->name = 'test';

        $this->dataPersister = new SingleTextDataPersister($this->dataPersisterObservable);
    }

    public function testDoesNotSupportNonSingleText() {
        $this->dataPersisterObservable
            ->method('supports')
            ->willReturn(true)
        ;

        $this->assertFalse($this->dataPersister->supports([], []));
    }

    public function testSetsRootFromParentOnCreate() {
        // when
        /** @var SingleText $data */
        $data = $this->dataPersister->beforeCreate($this->contentNode);

        // then
        $this->assertEquals($this->root, $data->root);
    }

    public function testCopyTextFromPrototypeOnCreate() {
        // when
        /** @var SingleText $data */
        $data = $this->dataPersister->beforeCreate($this->contentNode);

        // then
        $this->assertEquals($data->text, $this->contentNode->prototype->text);

        $this->assertEquals($data->instanceName, $this->contentNode->prototype->instanceName);
        $this->assertEquals($data->slot, $this->contentNode->prototype->slot);
        $this->assertEquals($data->position, $this->contentNode->prototype->position);
        $this->assertEquals($data->contentType, $this->contentNode->prototype->contentType);
    }

    public function testDoesNotOverrideTextOnCreate() {
        // given
        $this->contentNode->text = 'test111';

        // when
        /** @var SingleText $data */
        $data = $this->dataPersister->beforeCreate($this->contentNode);

        // then
        $this->assertNotEquals($data->text, $this->contentNode->prototype->text);
        $this->assertEquals($data->text, 'test111');
    }

    public function testDoesNotSetRootFromParentOnUpdate() {
        // when
        /** @var SingleText $data */
        $data = $this->dataPersister->beforeUpdate($this->contentNode);

        // then
        $this->assertNotEquals($this->root, $data->root);
    }

    public function testDoesNotCopyTextFromPrototypeOnUpdate() {
        // when
        /** @var SingleText $data */
        $data = $this->dataPersister->beforeUpdate($this->contentNode);

        // then
        $this->assertNotEquals($data->text, $this->contentNode->prototype->text);

        $this->assertNotEquals($data->instanceName, $this->contentNode->prototype->instanceName);
        $this->assertNotEquals($data->slot, $this->contentNode->prototype->slot);
        $this->assertNotEquals($data->position, $this->contentNode->prototype->position);
        $this->assertNotEquals($data->contentType, $this->contentNode->prototype->contentType);
    }
}
