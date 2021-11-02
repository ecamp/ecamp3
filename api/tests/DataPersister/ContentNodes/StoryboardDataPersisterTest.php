<?php

namespace App\Tests\DataPersister\ContentNodes;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\DataPersister\ContentNode\StoryboardDataPersister;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentNode\Storyboard;
use App\Entity\ContentNode\StoryboardSection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class StoryboardDataPersisterTest extends TestCase {
    private StoryboardDataPersister $dataPersister;
    private MockObject|ContextAwareDataPersisterInterface $decoratedMock;
    private Storyboard $contentNode;

    protected function setUp(): void {
        $this->decoratedMock = $this->createMock(ContextAwareDataPersisterInterface::class);
        $this->contentNode = new Storyboard();

        $this->root = $this->createMock(ColumnLayout::class);
        $this->contentNode->parent = new ColumnLayout();
        $this->contentNode->parent->root = $this->root;

        $prototype = new Storyboard();
        $this->contentNode->prototype = $prototype;

        $section = new StoryboardSection();
        $section->column1 = 'Column 1';
        $section->column2 = 'Column 2';
        $section->column3 = 'Column 3';

        $prototype->addSection($section);

        $this->dataPersister = new StoryboardDataPersister($this->decoratedMock);
    }

    public function testDelegatesSupportCheckToDecorated() {
        $this->decoratedMock
            ->expects($this->exactly(2))
            ->method('supports')
            ->willReturnOnConsecutiveCalls(true, false)
        ;

        $this->assertTrue($this->dataPersister->supports($this->contentNode, []));
        $this->assertFalse($this->dataPersister->supports($this->contentNode, []));
    }

    public function testDoesNotSupportNonStoryboard() {
        $this->decoratedMock
            ->method('supports')
            ->willReturn(true)
        ;

        $this->assertFalse($this->dataPersister->supports([], []));
    }

    public function testDelegatesPersistToDecorated() {
        // given
        $this->decoratedMock->expects($this->once())
            ->method('persist')
        ;

        // when
        $this->dataPersister->persist($this->contentNode, []);

        // then
    }

    public function testSetsRootFromParentOnCreate() {
        // given
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var Storyboard $data */
        $data = $this->dataPersister->persist($this->contentNode, ['collection_operation_name' => 'post']);

        // then
        $this->assertEquals($this->root, $data->root);
    }

    public function testCopyStoryboardSectionsFromPrototypeOnCreate() {
        // given
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var Storyboard $data */
        $data = $this->dataPersister->persist($this->contentNode, ['collection_operation_name' => 'post']);

        // then
        $this->assertEquals($data->sections[0]->column1, $this->contentNode->prototype->sections[0]->column1);
        $this->assertEquals($data->sections[0]->column2, $this->contentNode->prototype->sections[0]->column2);
        $this->assertEquals($data->sections[0]->column3, $this->contentNode->prototype->sections[0]->column3);
    }

    public function testDoesNotSetRootFromParentOnUpdate() {
        // given
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var Storyboard $data */
        $data = $this->dataPersister->persist($this->contentNode, ['item_operation_name' => 'patch']);

        // then
        $this->assertNotEquals($this->root, $data->root);
    }

    public function testDoesNotCopyStoryboardSectionsFromPrototypeOnUpdate() {
        // given
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var Storyboard $data */
        $data = $this->dataPersister->persist($this->contentNode, ['item_operation_name' => 'patch']);

        // then
        $this->assertEquals(count($data->sections), 0);
    }
}
