<?php

namespace App\Tests\DataPersister\ContentNodes;

use App\DataPersister\ContentNode\StoryboardDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentNode\Storyboard;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class StoryboardDataPersisterTest extends TestCase {
    private StoryboardDataPersister $dataPersister;
    private MockObject|DataPersisterObservable $dataPersisterObservable;
    private ColumnLayout $root;
    private Storyboard $contentNode;

    protected function setUp(): void {
        $this->dataPersisterObservable = $this->createMock(DataPersisterObservable::class);
        $this->contentNode = new Storyboard();

        $this->root = new ColumnLayout();
        $this->contentNode->parent = new ColumnLayout();
        $this->contentNode->parent->root = $this->root;

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

    public function testDoesNotSetRootFromParentOnUpdate() {
        // when
        /** @var Storyboard $data */
        $data = $this->dataPersister->beforeUpdate($this->contentNode);

        // then
        $this->assertNotEquals($this->root, $data->root);
    }
}
