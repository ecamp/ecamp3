<?php

namespace App\Tests\Doctrine\Filter;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\IriConverterInterface;
use App\Doctrine\Filter\MaterialItemPeriodFilter;
use App\Entity\MaterialItem;
use App\Entity\Period;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class MaterialItemPeriodFilterTest extends TestCase {
    private ManagerRegistry|MockObject $managerRegistryMock;
    private EntityRepository|MockObject $materialNodeRepositoryMock;
    private MockObject|QueryBuilder $materialNodeQueryBuilderMock;
    private MockObject|QueryBuilder $queryBuilderMock;
    private EntityManager|MockObject $entityManagerMock;
    private MockObject|QueryNameGeneratorInterface $queryNameGeneratorInterfaceMock;
    private IriConverterInterface|MockObject $iriConverterMock;

    public function setUp(): void {
        parent::setUp();
        $this->managerRegistryMock = $this->createMock(ManagerRegistry::class);
        $this->materialNodeRepositoryMock = $this->createMock(EntityRepository::class);
        $this->materialNodeQueryBuilderMock = $this->createMock(QueryBuilder::class);
        $this->queryBuilderMock = $this->createMock(QueryBuilder::class);
        $this->entityManagerMock = $this->createMock(EntityManager::class);
        $this->queryNameGeneratorInterfaceMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->iriConverterMock = $this->createMock(IriConverterInterface::class);

        $this->managerRegistryMock
            ->method('getRepository')
            ->will($this->returnValue($this->materialNodeRepositoryMock))
        ;

        $this->materialNodeRepositoryMock
            ->method('createQueryBuilder')
            ->will($this->returnValue($this->materialNodeQueryBuilderMock))
        ;

        $this->materialNodeQueryBuilderMock
            ->method('select')
            ->will($this->returnSelf())
        ;

        $this->materialNodeQueryBuilderMock
            ->method('join')
            ->will($this->returnSelf())
        ;

        $this->materialNodeQueryBuilderMock
            ->method('where')
            ->will($this->returnSelf())
        ;

        $this->queryBuilderMock
            ->method('from')
            ->will($this->returnSelf())
        ;

        $this->queryBuilderMock
            ->method('join')
            ->will($this->returnSelf())
        ;

        $this->queryBuilderMock
            ->method('where')
            ->will($this->returnSelf())
        ;

        $this->queryBuilderMock
            ->method('getRootAliases')
            ->willReturn(['o'])
        ;

        $this->queryBuilderMock
            ->method('getEntityManager')
            ->will($this->returnValue($this->entityManagerMock))
        ;

        $this->queryBuilderMock
            ->method('getParameters')
            ->willReturn(new ArrayCollection())
        ;

        $expr = new Expr();
        $this->queryBuilderMock
            ->method('expr')
            ->will($this->returnValue($expr))
        ;

        $this->entityManagerMock
            ->method('createQueryBuilder')
            ->will($this->returnValue($this->queryBuilderMock))
        ;

        $this->queryNameGeneratorInterfaceMock
            ->method('generateParameterName')
            ->willReturnCallback(fn ($field) => $field.'_a1')
        ;
        $this->queryNameGeneratorInterfaceMock
            ->method('generateJoinAlias')
            ->willReturnCallback(fn ($field) => $field.'_j1')
        ;
    }

    public function testGetDescription() {
        // given
        $filter = new MaterialItemPeriodFilter($this->iriConverterMock, $this->managerRegistryMock);

        // when
        $description = $filter->getDescription('Dummy');

        // then
        $this->assertEquals([
            'period' => [
                'property' => 'period',
                'type' => 'string',
                'required' => false,
            ],
        ], $description);
    }

    public function testFailsForResouceClassOtherThanMaterialItem() {
        // given
        $filter = new MaterialItemPeriodFilter($this->iriConverterMock, $this->managerRegistryMock);

        // then
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('MaterialItemPeriodFilter can only be applied to entities of type MaterialItem (received: DummyClass).');

        // when
        $filter->apply($this->queryBuilderMock, $this->queryNameGeneratorInterfaceMock, 'DummyClass', null, ['filters' => [
            'period' => '/period/123',
        ]]);
    }

    public function testDoesNothingForPropertiesOtherThanPeriod() {
        // given
        $filter = new MaterialItemPeriodFilter($this->iriConverterMock, $this->managerRegistryMock);

        // then
        $this->queryBuilderMock
            ->expects($this->never())
            ->method('getRootAliases')
        ;

        $this->queryBuilderMock
            ->expects($this->never())
            ->method('andWhere')
        ;

        // when
        $filter->apply($this->queryBuilderMock, $this->queryNameGeneratorInterfaceMock, MaterialItem::class, null, ['filters' => [
            'dummyProperty' => 'abc',
        ]]);
    }

    public function testAddsFilterForPeriodProperty() {
        // given
        $filter = new MaterialItemPeriodFilter($this->iriConverterMock, $this->managerRegistryMock);
        $period = new Period();

        // then
        $this->iriConverterMock
            ->expects($this->once())
            ->method('getResourceFromIri')
            ->with('/period/123')
            ->will($this->returnValue($period))
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
        $filter->apply($this->queryBuilderMock, $this->queryNameGeneratorInterfaceMock, MaterialItem::class, null, ['filters' => [
            'period' => '/period/123',
        ]]);
    }
}
