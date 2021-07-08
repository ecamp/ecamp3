<?php

namespace App\Tests\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\DataPersister\CategoryDataPersister;
use App\Entity\Category;
use App\Entity\ContentType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class CategoryDataPersisterTest extends TestCase {
    private CategoryDataPersister $dataPersister;
    private MockObject | ContextAwareDataPersisterInterface $decoratedMock;
    private MockObject | EntityManagerInterface $entityManagerMock;
    private Category $category;

    protected function setUp(): void {
        $this->decoratedMock = $this->createMock(ContextAwareDataPersisterInterface::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->category = new Category();
        $this->category->category = new Category();

        $this->dataPersister = new CategoryDataPersister($this->decoratedMock, $this->entityManagerMock);
    }

    public function testDelegatesSupportCheckToDecorated() {
        $this->decoratedMock
            ->expects($this->exactly(2))
            ->method('supports')
            ->willReturnOnConsecutiveCalls(true, false)
        ;

        $this->assertTrue($this->dataPersister->supports($this->category, []));
        $this->assertFalse($this->dataPersister->supports($this->category, []));
    }

    public function testDoesNotSupportNonCategory() {
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
        $this->dataPersister->persist($this->category, []);

        // then
    }

    public function testPostCreatesANewRootContentNode() {
        // given
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);
        $repositoryMock = $this->createMock(EntityRepository::class);
        $repositoryMock->method('findOneBy')->willReturnCallback(function ($criteria) {
            $result = new ContentType();
            $result->name = $criteria['name'];

            return $result;
        });
        $this->entityManagerMock->method('getRepository')->willReturn($repositoryMock);

        // when
        /** @var Category $data */
        $data = $this->dataPersister->persist($this->category, ['collection_operation_name' => 'post']);

        // then
        $this->assertNotNull($data->getRootContentNode());
        $this->assertEquals('ColumnLayout', $data->getRootContentNode()->contentType->name);
    }

    public function testUpdateDoesNotChangeRootContentNode() {
        // given
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var Category $data */
        $data = $this->dataPersister->persist($this->category, ['item_operation_name' => 'patch']);

        // then
        $this->assertNull($data->getRootContentNode());
    }
}
