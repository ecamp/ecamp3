<?php

namespace App\Tests\DataPersister\ContentNodes;

use App\DataPersister\ContentNode\MultiSelectDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
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
    private MockObject|DataPersisterObservable $dataPersisterObservable;
    private MultiSelect $contentNode;

    protected function setUp(): void {
        $this->dataPersisterObservable = $this->createMock(DataPersisterObservable::class);
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

        $this->dataPersister = new MultiSelectDataPersister($this->dataPersisterObservable);
    }

    public function testDoesNotSupportNonMultiSelect() {
        $this->dataPersisterObservable
            ->method('supports')
            ->willReturn(true)
        ;

        $this->assertFalse($this->dataPersister->supports([], []));
    }

    public function testSetsRootFromParentOnCreate() {
        // when
        /** @var MultiSelect $data */
        $data = $this->dataPersister->beforeCreate($this->contentNode);

        // then
        $this->assertEquals($this->root, $data->root);
    }

    public function testCopyMultiSelectOptionsFromContentTypeOnCreate() {
        // when
        /** @var MultiSelect $data */
        $data = $this->dataPersister->beforeCreate($this->contentNode);

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

        // when
        /** @var MultiSelect $data */
        $data = $this->dataPersister->beforeCreate($this->contentNode);

        // then
        $this->assertEquals($data->options[0]->translateKey, $this->contentNode->prototype->options[0]->translateKey);
        $this->assertEquals($data->options[0]->checked, $this->contentNode->prototype->options[0]->checked);
        $this->assertEquals($data->options[0]->getPos(), $this->contentNode->prototype->options[0]->getPos());

        $this->assertEquals($data->instanceName, $this->contentNode->prototype->instanceName);
        $this->assertEquals($data->slot, $this->contentNode->prototype->slot);
        $this->assertEquals($data->position, $this->contentNode->prototype->position);

        $this->assertNotEquals($data->contentType, $this->contentNode->prototype->contentType);
    }

    public function testDoesNotSetRootFromParentOnUpdate() {
        // when
        /** @var MultiSelect $data */
        $data = $this->dataPersister->beforeUpdate($this->contentNode);

        // then
        $this->assertNotEquals($this->root, $data->root);
    }

    public function testDoesNotCopyMultiSelectOptionsOnUpdate() {
        // when
        /** @var MultiSelect $data */
        $data = $this->dataPersister->beforeUpdate($this->contentNode);

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

        $prototype->instanceName = 'instance';
        $prototype->slot = 'left';
        $prototype->position = 99;
        $prototype->contentType = new ContentType();
        $prototype->contentType->name = 'test';

        $this->contentNode->prototype = $prototype;
    }
}
