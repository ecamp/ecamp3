<?php

namespace App\Tests\State\ContentNodes;

use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentNode\Storyboard;
use App\InputFilter\CleanHTMLFilter;
use App\InputFilter\CleanTextFilter;
use App\State\ContentNode\StoryboardPersistProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class StoryboardPersistProcessorTest extends TestCase {
    private StoryboardPersistProcessor $dataPersister;
    private MockObject|CleanHTMLFilter $cleanHTMLFilter;
    private MockObject|CleanTextFilter $cleanTextFilter;
    private ColumnLayout $root;
    private Storyboard $contentNode;

    protected function setUp(): void {
        $decoratedProcessor = $this->createMock(ProcessorInterface::class);

        $this->cleanHTMLFilter = $this->createMock(CleanHTMLFilter::class);
        $this->cleanHTMLFilter->method('applyTo')->will(
            $this->returnCallback(
                function ($object, $property) {
                    $object[$property] = '***sanitizedHTML***';

                    return $object;
                }
            )
        );

        $this->cleanTextFilter = $this->createMock(CleanTextFilter::class);
        $this->cleanTextFilter->method('applyTo')->will(
            $this->returnCallback(
                function ($object, $property) {
                    $object[$property] = '***sanitizedText***';

                    return $object;
                }
            )
        );

        $this->contentNode = new Storyboard();
        $this->contentNode->data = ['sections' => [
            '37bbd7b8-441e-403e-b227-70ea52170b9b' => [
                'column1' => "test1\n\t",
                'column2Html' => "test2\n\t",
                'column3' => "test3\n\t",
                'position' => 0,
            ],
            '2c5534d3-d077-4ccd-8c9e-b961b451f6e3' => [
                'column1' => "test1\n\t",
                'column2Html' => "test2\n\t",
                'column3' => "test3\n\t",
                'position' => 1,
            ],
        ]];

        $this->root = new ColumnLayout();
        $this->contentNode->parent = new ColumnLayout();
        $this->contentNode->parent->root = $this->root;

        $this->dataPersister = new StoryboardPersistProcessor($decoratedProcessor, $this->cleanHTMLFilter, $this->cleanTextFilter);
    }

    public function testSetsRootFromParentOnCreate() {
        // when
        /** @var Storyboard $data */
        $data = $this->dataPersister->onBefore($this->contentNode, new Post());

        // then
        $this->assertEquals($this->root, $data->root);
    }

    public function testDoesNotSetRootFromParentOnUpdate() {
        // when
        /** @var Storyboard $data */
        $data = $this->dataPersister->onBefore($this->contentNode, new Patch());

        // then
        $this->assertNotEquals($this->root, $data->root);
    }

    public function testSantizeDataOnCreate() {
        // when
        /** @var Storyboard $data */
        $data = $this->dataPersister->onBefore($this->contentNode, new Post());

        // then
        $this->assertEquals(['sections' => [
            '37bbd7b8-441e-403e-b227-70ea52170b9b' => [
                'column1' => '***sanitizedText***',
                'column2Html' => '***sanitizedHTML***',
                'column3' => '***sanitizedText***',
                'position' => 0,
            ],
            '2c5534d3-d077-4ccd-8c9e-b961b451f6e3' => [
                'column1' => '***sanitizedText***',
                'column2Html' => '***sanitizedHTML***',
                'column3' => '***sanitizedText***',
                'position' => 1,
            ],
        ]], $data->data);
    }

    public function testSanitizeOnUpdate() {
        // when
        /** @var Storyboard $data */
        $data = $this->dataPersister->onBefore($this->contentNode, new Patch());

        // then
        // then
        $this->assertEquals(['sections' => [
            '37bbd7b8-441e-403e-b227-70ea52170b9b' => [
                'column1' => '***sanitizedText***',
                'column2Html' => '***sanitizedHTML***',
                'column3' => '***sanitizedText***',
                'position' => 0,
            ],
            '2c5534d3-d077-4ccd-8c9e-b961b451f6e3' => [
                'column1' => '***sanitizedText***',
                'column2Html' => '***sanitizedHTML***',
                'column3' => '***sanitizedText***',
                'position' => 1,
            ],
        ]], $data->data);
    }
}
