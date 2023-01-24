<?php

namespace App\Tests\State;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Category;
use App\Entity\ContentNode\ColumnLayout;
use App\State\CategoryRemoveProcessor;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class CategoryRemoveProcessorTest extends TestCase {
    private CategoryRemoveProcessor $processor;
    private Category $category;
    private MockObject|EntityManagerInterface $em;

    protected function setUp(): void {
        $decoratedProcessor = $this->createMock(ProcessorInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);

        $this->category = new Category();

        $root = new ColumnLayout();
        $this->category->setRootContentNode($root);

        $this->processor = new CategoryRemoveProcessor($decoratedProcessor, $this->em);
    }

    public function testRemovesRootContentNode() {
        // then
        $this->em->expects($this->exactly(1))->method('remove')->with($this->category->getRootContentNode());

        // when
        $this->processor->onBefore($this->category, new Delete());
    }
}
