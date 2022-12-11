<?php

namespace App\Tests\State\ContentNodes;

use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentNode\MultiSelect;
use App\Entity\ContentType;
use App\State\ContentNode\MultiSelectCreateProcessor;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class MultiSelectCreateProcessorTest extends TestCase {
    private MultiSelectCreateProcessor $processor;
    private ColumnLayout $root;
    private MultiSelect $contentNode;

    protected function setUp(): void {
        $decoratedProcessor = $this->createMock(ProcessorInterface::class);
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

        $this->processor = new MultiSelectCreateProcessor($decoratedProcessor);
    }

    public function testSetsRootFromParentOnCreate() {
        // when
        /** @var MultiSelect $data */
        $data = $this->processor->onBefore($this->contentNode, new Post());

        // then
        $this->assertEquals($this->root, $data->root);
    }

    public function testCopyMultiSelectOptionsFromContentTypeOnCreate() {
        // when
        /** @var MultiSelect $data */
        $data = $this->processor->onBefore($this->contentNode, new Post());

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
        $data = $this->processor->onBefore($this->contentNode, new Patch());

        // then
        $this->assertNotEquals($this->root, $data->root);
    }

    public function testDoesNotCopyMultiSelectOptionsOnUpdate() {
        // when
        /** @var MultiSelect $data */
        $data = $this->processor->onBefore($this->contentNode, new Patch());

        // then
        $this->assertEquals($data->data, $this->contentNode->data);
    }
}
