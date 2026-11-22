<?php

namespace App\Tests\State\ContentNodes;

use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentNode\Storyboard;
use App\InputFilter\CleanHTMLFilter;
use App\InputFilter\CleanTextFilter;
use App\Service\Image\ImageReferenceValidator;
use App\State\ContentNode\StoryboardPersistProcessor;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class StoryboardPersistProcessorTest extends TestCase {
    private StoryboardPersistProcessor $processor;
    private ColumnLayout $root;
    private Storyboard $contentNode;

    protected function setUp(): void {
        $decoratedProcessor = $this->createStub(ProcessorInterface::class);

        $cleanHTMLFilter = $this->createStub(CleanHTMLFilter::class);
        $cleanHTMLFilter->method('applyTo')->willReturnCallback(
            function (array $object, string $property): array {
                $object[$property] = '***sanitizedHTML***';

                return $object;
            }
        );

        $cleanTextFilter = $this->createStub(CleanTextFilter::class);
        $cleanTextFilter->method('applyTo')->willReturnCallback(
            function (array $object, string $property): array {
                $object[$property] = '***sanitizedText***';

                return $object;
            }
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

        $this->processor = new StoryboardPersistProcessor($decoratedProcessor, $cleanHTMLFilter, $cleanTextFilter, new ImageReferenceValidator($this->createStub(EntityManagerInterface::class)));
    }

    public function testSetsRootFromParentOnCreate() {
        // when
        $data = $this->processor->onBefore($this->contentNode, new Post());

        // then
        $this->assertEquals($this->root, $data->root);
    }

    public function testDoesNotSetRootFromParentOnUpdate() {
        // when
        $data = $this->processor->onBefore($this->contentNode, new Patch());

        // then
        $this->assertNotEquals($this->root, $data->root);
    }

    public function testSetDefaultValueOnCreate() {
        // given
        $this->contentNode->data = null;

        // when
        $data = $this->processor->onBefore($this->contentNode, new Post());

        // then
        $newSection = $data->data['sections'][array_keys($data->data['sections'])[0]];
        $this->assertEquals([
            'column1' => '***sanitizedText***',
            'column2Html' => '***sanitizedHTML***',
            'column3' => '***sanitizedText***',
            'position' => 0,
        ], $newSection);
    }

    public function testSantizeDataOnCreate() {
        // when
        $data = $this->processor->onBefore($this->contentNode, new Post());

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
        $data = $this->processor->onBefore($this->contentNode, new Patch());

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
