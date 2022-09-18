<?php

namespace App\Tests\DataPersister\ContentNodes;

use App\DataPersister\ContentNode\SingleTextDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentNode\SingleText;
use App\InputFilter\CleanHTMLFilter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class SingleTextDataPersisterTest extends TestCase {
    private SingleTextDataPersister $dataPersister;
    private MockObject|DataPersisterObservable $dataPersisterObservable;
    private MockObject|CleanHTMLFilter $cleanHTMLFilter;
    private ColumnLayout $root;
    private SingleText $contentNode;

    protected function setUp(): void {
        $this->dataPersisterObservable = $this->createMock(DataPersisterObservable::class);
        $this->cleanHTMLFilter = $this->createMock(CleanHTMLFilter::class);
        $this->contentNode = new SingleText();

        $this->root = new ColumnLayout();
        $this->contentNode->parent = new SingleText();
        $this->contentNode->parent->root = $this->root;

        $this->dataPersister = new SingleTextDataPersister($this->dataPersisterObservable, $this->cleanHTMLFilter);
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

    public function testDoesNotSetRootFromParentOnUpdate() {
        // when
        /** @var SingleText $data */
        $data = $this->dataPersister->beforeUpdate($this->contentNode);

        // then
        $this->assertNotEquals($this->root, $data->root);
    }
}
