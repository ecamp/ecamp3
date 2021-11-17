<?php

namespace App\Tests\DataPersister\ContentNodes;

use App\DataPersister\ContentNode\StoryboardDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentNode\Storyboard;
use App\Entity\ContentNode\StoryboardSection;
use App\Entity\ContentType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class StoryboardDataPersisterTest extends TestCase {
    private StoryboardDataPersister $dataPersister;
    private MockObject|DataPersisterObservable $dataPersisterObservable;
    private Storyboard $contentNode;

    protected function setUp(): void {
        $this->dataPersisterObservable = $this->createMock(DataPersisterObservable::class);
        $this->contentNode = new Storyboard();

        $this->root = $this->createMock(ColumnLayout::class);
        $this->contentNode->parent = new ColumnLayout();
        $this->contentNode->parent->root = $this->root;

        $prototype = new Storyboard();
        $prototype->instanceName = 'instance';
        $prototype->slot = 'left';
        $prototype->position = 99;
        $prototype->contentType = new ContentType();
        $prototype->contentType->name = 'test';
        $this->contentNode->prototype = $prototype;

        $section = new StoryboardSection();
        $section->column1 = 'Column 1';
        $section->column2 = 'Column 2';
        $section->column3 = 'Column 3';

        $prototype->addSection($section);

        $this->dataPersister = new StoryboardDataPersister($this->dataPersisterObservable);
    }

    public function testDoesNotSupportNonStoryboard() {
        $this->dataPersisterObservable
            ->method('supports')
            ->willReturn(true)
        ;

        $this->assertFalse($this->dataPersister->supports([], []));
    }

    public function testSetsRootFromParentOnCreate() {
        // when
        /** @var Storyboard $data */
        $data = $this->dataPersister->beforeCreate($this->contentNode);

        // then
        $this->assertEquals($this->root, $data->root);
    }

    public function testCopyStoryboardSectionsFromPrototypeOnCreate() {
        // when
        /** @var Storyboard $data */
        $data = $this->dataPersister->beforeCreate($this->contentNode);

        // then
        $this->assertEquals($data->sections[0]->column1, $this->contentNode->prototype->sections[0]->column1);
        $this->assertEquals($data->sections[0]->column2, $this->contentNode->prototype->sections[0]->column2);
        $this->assertEquals($data->sections[0]->column3, $this->contentNode->prototype->sections[0]->column3);

        $this->assertEquals($data->instanceName, $this->contentNode->prototype->instanceName);
        $this->assertEquals($data->slot, $this->contentNode->prototype->slot);
        $this->assertEquals($data->position, $this->contentNode->prototype->position);
        $this->assertEquals($data->contentType, $this->contentNode->prototype->contentType);
    }

    public function testDoesNotSetRootFromParentOnUpdate() {
        // when
        /** @var Storyboard $data */
        $data = $this->dataPersister->beforeUpdate($this->contentNode);

        // then
        $this->assertNotEquals($this->root, $data->root);
    }

    public function testDoesNotCopyStoryboardSectionsFromPrototypeOnUpdate() {
        // when
        /** @var Storyboard $data */
        $data = $this->dataPersister->beforeUpdate($this->contentNode);

        // then
        $this->assertEquals(count($data->sections), 0);

        $this->assertNotEquals($data->instanceName, $this->contentNode->prototype->instanceName);
        $this->assertNotEquals($data->slot, $this->contentNode->prototype->slot);
        $this->assertNotEquals($data->position, $this->contentNode->prototype->position);
        $this->assertNotEquals($data->contentType, $this->contentNode->prototype->contentType);
    }
}
