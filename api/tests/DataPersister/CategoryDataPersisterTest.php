<?php

namespace App\Tests\DataPersister;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\DataPersister\CategoryDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\Category;
use App\Entity\ContentType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\isNull;
use PHPUnit\Framework\MockObject\MockObject;
use function PHPUnit\Framework\once;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class CategoryDataPersisterTest extends TestCase {
    private CategoryDataPersister $dataPersister;
    private MockObject|ValidatorInterface $validator;
    private MockObject|EntityManagerInterface $entityManagerMock;
    private Category $category;

    protected function setUp(): void {
        $this->category = new Category();

        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $dataPersisterObservable = $this->createMock(DataPersisterObservable::class);
        $this->dataPersister = new CategoryDataPersister(
            $dataPersisterObservable,
            $this->validator,
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
        $data = $this->dataPersister->beforeCreate($this->category);

        // then
        $this->assertNotNull($data->getRootContentNode());
        $this->assertEquals('ColumnLayout', $data->getRootContentNode()->contentType->name);
    }

    public function testCallsValidatorOnRemove() {
        $this->validator->expects(once())->method('validate');

        $remove = $this->dataPersister->beforeRemove($this->category);

        assertThat($remove, isNull());
    }
}
