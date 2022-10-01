<?php

namespace App\Tests\DataPersister\ContentNodes;

use App\DataPersister\ContentNode\StoryboardDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentNode\Storyboard;
use App\InputFilter\CleanHTMLFilter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class StoryboardDataPersisterTest extends TestCase {
    private StoryboardDataPersister $dataPersister;
    private MockObject|DataPersisterObservable $dataPersisterObservable;
    private MockObject|CleanHTMLFilter $cleanHTMLFilter;
    private ColumnLayout $root;
    private Storyboard $contentNode;

    protected function setUp(): void {
        $this->dataPersisterObservable = $this->createMock(DataPersisterObservable::class);
        $this->cleanHTMLFilter = $this->createMock(CleanHTMLFilter::class);
        $this->cleanHTMLFilter->method('applyTo')->will(
            $this->returnCallback(
                function ($object, $property) {
                    $object[$property] = '***sanitzed***';

                    return $object;
                }
            )
        );

        $this->contentNode = new Storyboard();
        $this->contentNode->data = ['sections' => [
            '37bbd7b8-441e-403e-b227-70ea52170b9b' => [
                'column1' => 'test1<script>alert(1)</script>',
                'column2' => 'test2<script>alert(2)</script>',
                'column3' => 'test3<script>alert(3)</script>',
                'position' => 0,
            ],
            '2c5534d3-d077-4ccd-8c9e-b961b451f6e3' => [
                'column1' => 'test1<script>alert(1)</script>',
                'column2' => 'test2<script>alert(2)</script>',
                'column3' => 'test3<script>alert(3)</script>',
                'position' => 1,
            ],
        ]];

        $this->root = new ColumnLayout();
        $this->contentNode->parent = new ColumnLayout();
        $this->contentNode->parent->root = $this->root;

        $this->dataPersister = new StoryboardDataPersister($this->dataPersisterObservable, $this->cleanHTMLFilter);
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

    public function testSantizeDataOnCreate() {
        // when
        /** @var Storyboard $data */
        $data = $this->dataPersister->beforeCreate($this->contentNode);

        // then
        $this->assertEquals(['sections' => [
            '37bbd7b8-441e-403e-b227-70ea52170b9b' => [
                'column1' => '***sanitzed***',
                'column2' => '***sanitzed***',
                'column3' => '***sanitzed***',
                'position' => 0,
            ],
            '2c5534d3-d077-4ccd-8c9e-b961b451f6e3' => [
                'column1' => '***sanitzed***',
                'column2' => '***sanitzed***',
                'column3' => '***sanitzed***',
                'position' => 1,
            ],
        ]], $data->data);
    }

    public function testSanitizeOnUpdate() {
        // when
        /** @var Storyboard $data */
        $data = $this->dataPersister->beforeUpdate($this->contentNode);

        // then
        // then
        $this->assertEquals(['sections' => [
            '37bbd7b8-441e-403e-b227-70ea52170b9b' => [
                'column1' => '***sanitzed***',
                'column2' => '***sanitzed***',
                'column3' => '***sanitzed***',
                'position' => 0,
            ],
            '2c5534d3-d077-4ccd-8c9e-b961b451f6e3' => [
                'column1' => '***sanitzed***',
                'column2' => '***sanitzed***',
                'column3' => '***sanitzed***',
                'position' => 1,
            ],
        ]], $data->data);
    }
}
