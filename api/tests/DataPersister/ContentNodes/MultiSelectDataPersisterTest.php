<?php

namespace App\Tests\DataPersister\ContentNodes;

use App\DataPersister\ContentNode\MultiSelectDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentNode\MultiSelect;
use App\Entity\ContentType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class MultiSelectDataPersisterTest extends TestCase {
    private MultiSelectDataPersister $dataPersister;
    private MockObject|DataPersisterObservable $dataPersisterObservable;
    private ColumnLayout $root;
    private MultiSelect $contentNode;

    protected function setUp(): void {
        $this->dataPersisterObservable = $this->createMock(DataPersisterObservable::class);
        $this->contentNode = new MultiSelect();

        $this->root = new ColumnLayout();
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
        $this->assertArrayHasKey('options', $data->data);

        $options = $data->data['options'];

        $this->assertArrayHasKey('key1', $options);
        $this->assertEquals($options['key1']['checked'], false);

        $this->assertArrayHasKey('key2', $options);
        $this->assertEquals($options['key2']['checked'], false);
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
        $this->assertEquals($data->data, $this->contentNode->data);
    }
}
