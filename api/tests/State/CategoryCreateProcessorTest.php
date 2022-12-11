<?php

namespace App\Tests\State;

use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Category;
use App\Entity\ContentType;
use App\State\CategoryCreateProcessor;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class CategoryDataPersisterTest extends TestCase {
    private CategoryCreateProcessor $processor;
    private MockObject|EntityManagerInterface $entityManagerMock;
    private Category $category;

    protected function setUp(): void {
        $this->category = new Category();

        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $decoratedProcessor = $this->createMock(ProcessorInterface::class);
        $this->processor = new CategoryCreateProcessor(
            $decoratedProcessor,
            $this->entityManagerMock
        );
    }

    public function testCreatesNewContentNodeBeforeCreate() {
        // given
        $repositoryMock = $this->createMock(EntityRepository::class);
        $repositoryMock->method('findOneBy')->willReturnCallback(function ($criteria) {
            $result = new ContentType();
            $result->name = $criteria['name'];

            return $result;
        });
        $this->entityManagerMock->method('getRepository')->willReturn($repositoryMock);

        // when
        /** @var Category $data */
        $data = $this->processor->onBefore($this->category, new Post());

        // then
        $this->assertNotNull($data->getRootContentNode());
        $this->assertEquals('ColumnLayout', $data->getRootContentNode()->contentType->name);
    }
}
