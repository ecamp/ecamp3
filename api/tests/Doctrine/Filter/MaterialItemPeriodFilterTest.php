<?php

namespace App\Tests\Doctrine\Filter;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Parameters;
use ApiPlatform\Metadata\QueryParameter;
use App\Doctrine\Filter\MaterialItemPeriodFilter;
use App\Entity\MaterialItem;
use App\Entity\Period;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[AllowMockObjectsWithoutExpectations]
class MaterialItemPeriodFilterTest extends TestCase {
    private ManagerRegistry|MockObject $managerRegistryMock;
    private MockObject|QueryBuilder $queryBuilderMock;
    private MockObject|QueryNameGeneratorInterface $queryNameGeneratorInterfaceMock;
    private IriConverterInterface|MockObject $iriConverterMock;

    public function setUp(): void {
        parent::setUp();
        $this->managerRegistryMock = $this->createStub(ManagerRegistry::class);
        $materialNodeRepositoryMock = $this->createStub(EntityRepository::class);
        $materialNodeQueryBuilderMock = $this->createStub(QueryBuilder::class);
        $this->queryBuilderMock = $this->createMock(QueryBuilder::class);
        $entityManagerMock = $this->createStub(EntityManager::class);
        $this->queryNameGeneratorInterfaceMock = $this->createStub(QueryNameGeneratorInterface::class);
        $this->iriConverterMock = $this->createMock(IriConverterInterface::class);

        $this->managerRegistryMock
            ->method('getRepository')
            ->willReturn($materialNodeRepositoryMock)
        ;

        $materialNodeRepositoryMock
            ->method('createQueryBuilder')
            ->willReturn($materialNodeQueryBuilderMock)
        ;

        $materialNodeQueryBuilderMock
            ->method('select')->willReturnSelf()
        ;

        $materialNodeQueryBuilderMock
            ->method('join')->willReturnSelf()
        ;

        $materialNodeQueryBuilderMock
            ->method('where')->willReturnSelf()
        ;

        $this->queryBuilderMock
            ->method('from')->willReturnSelf()
        ;

        $this->queryBuilderMock
            ->method('join')->willReturnSelf()
        ;

        $this->queryBuilderMock
            ->method('where')->willReturnSelf()
        ;

        $this->queryBuilderMock
            ->method('getRootAliases')
            ->willReturn(['o'])
        ;

        $this->queryBuilderMock
            ->method('getEntityManager')
            ->willReturn($entityManagerMock)
        ;

        $this->queryBuilderMock
            ->method('getParameters')
            ->willReturn(new ArrayCollection())
        ;

        $expr = new Expr();
        $this->queryBuilderMock
            ->method('expr')
            ->willReturn($expr)
        ;

        $entityManagerMock
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilderMock)
        ;

        $this->queryNameGeneratorInterfaceMock
            ->method('generateParameterName')
            ->willReturnCallback(fn (string $field): string => $field.'_a1')
        ;
        $this->queryNameGeneratorInterfaceMock
            ->method('generateJoinAlias')
            ->willReturnCallback(fn (string $field): string => $field.'_j1')
        ;
    }

    public function testIsDeclaredAsQueryParameterOnMainCollectionOperation() {
        $resourceClasses = [
            MaterialItem::class,
        ];

        foreach ($resourceClasses as $resourceClass) {
            $operations = (new \ReflectionClass($resourceClass))->getAttributes(ApiResource::class)[0]->newInstance()->getOperations();
            $mainCollection = array_values(array_filter(iterator_to_array($operations), fn ($operation) => $operation instanceof GetCollection))[0];
            $parameters = $mainCollection->getParameters();
            $parameter = $parameters instanceof Parameters ? $parameters->get('period') : $parameters['period'];

            $this->assertSame(MaterialItemPeriodFilter::class, $parameter->getFilter(), $resourceClass);
        }
    }

    public function testFailsForResouceClassOtherThanMaterialItem() {
        // given
        $filter = new MaterialItemPeriodFilter($this->iriConverterMock);

        // then
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('MaterialItemPeriodFilter can only be applied to entities of type MaterialItem (received: DummyClass).');

        // when
        $filter->apply($this->queryBuilderMock, $this->queryNameGeneratorInterfaceMock, 'DummyClass', null, ['parameter' => $this->parameter('/period/123')]);
    }

    public function testPropagatesUnresolvableIri() {
        // given
        $filter = new MaterialItemPeriodFilter($this->iriConverterMock);
        $this->iriConverterMock
            ->method('getResourceFromIri')
            ->willThrowException(new InvalidArgumentException('Not found'))
        ;

        // then
        $this->expectException(InvalidArgumentException::class);
        $this->queryBuilderMock
            ->expects($this->never())
            ->method('setParameter')
        ;

        // when
        $filter->apply($this->queryBuilderMock, $this->queryNameGeneratorInterfaceMock, MaterialItem::class, null, ['parameter' => $this->parameter('/periods/123')]);
    }

    public function testAddsFilterForPeriodProperty() {
        // given
        $filter = new MaterialItemPeriodFilter($this->iriConverterMock);
        $period = new Period();

        // then
        $this->iriConverterMock
            ->expects($this->once())
            ->method('getResourceFromIri')
            ->with('/period/123')
            ->willReturn($period)
        ;

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('andWhere')
        ;

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('setParameter')
            ->with('period_a1', $period)
        ;

        // when
        $filter->apply($this->queryBuilderMock, $this->queryNameGeneratorInterfaceMock, MaterialItem::class, null, ['parameter' => $this->parameter('/period/123')]);
    }

    private function parameter(string $value): QueryParameter {
        return (new QueryParameter(key: 'period'))->withValue($value);
    }
}
