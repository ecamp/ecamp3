<?php

namespace App\Tests\DataPersister;

use App\DataPersister\CategoryDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
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
    private MockObject|EntityManagerInterface $entityManagerMock;
    private Category $category;

    protected function setUp(): void {
        $this->category = new Category();

        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $dataPersisterObservable = $this->createMock(DataPersisterObservable::class);
        $this->dataPersister = new CategoryDataPersister($dataPersisterObservable, $this->entityManagerMock);
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
        $data = $this->dataPersister->beforeCreate($this->category);

        // then
        $this->assertNotNull($data->getRootContentNode());
        $this->assertEquals('ColumnLayout', $data->getRootContentNode()->contentType->name);
    }
}
