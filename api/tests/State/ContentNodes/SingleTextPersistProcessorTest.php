<?php

namespace App\Tests\State\ContentNodes;

use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentNode\SingleText;
use App\InputFilter\CleanHTMLFilter;
use App\State\ContentNode\SingleTextPersistProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class SingleTextPersistProcessorTest extends TestCase {
    private SingleTextPersistProcessor $processor;

    private MockObject|CleanHTMLFilter $cleanHTMLFilter;
    private ColumnLayout $root;
    private SingleText $contentNode;

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

        $this->contentNode = new SingleText();
        $this->contentNode->data = [
            'html' => 'test',
        ];

        $this->root = new ColumnLayout();
        $this->contentNode->parent = new SingleText();
        $this->contentNode->parent->root = $this->root;

        $this->processor = new SingleTextPersistProcessor($decoratedProcessor, $this->cleanHTMLFilter);
    }

    public function testSetsRootFromParentOnCreate() {
        // when
        /** @var SingleText $data */
        $data = $this->processor->onBefore($this->contentNode, new Post());

        // then
        $this->assertEquals($this->root, $data->root);
    }

    public function testDoesNotSetRootFromParentOnUpdate() {
        // when
        /** @var SingleText $data */
        $data = $this->processor->onBefore($this->contentNode, new Patch());

        // then
        $this->assertNotEquals($this->root, $data->root);
    }

    public function testSantizeData() {
        // when
        /** @var SingleText $data */
        $data = $this->processor->onBefore($this->contentNode, new Post());

        // then
        $this->assertEquals('***sanitizedHTML***', $data->data['html']);
    }
}
