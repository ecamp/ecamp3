<?php

namespace App\Tests\Doctrine\Filter;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Parameters;
use ApiPlatform\Metadata\QueryParameter;
use App\Doctrine\Filter\ContentNodePeriodFilter;
use App\Entity\ContentNode;
use App\Entity\ContentNode\ChecklistNode;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentNode\MaterialNode;
use App\Entity\ContentNode\MultiSelect;
use App\Entity\ContentNode\NoData;
use App\Entity\ContentNode\ResponsiveLayout;
use App\Entity\ContentNode\SingleText;
use App\Entity\ContentNode\Storyboard;
use App\Entity\Period;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
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
class ContentNodePeriodFilterTest extends TestCase {
    private ManagerRegistry|MockObject $managerRegistryMock;
    private MockObject|QueryBuilder $queryBuilderMock;
    private MockObject|QueryNameGeneratorInterface $queryNameGeneratorInterfaceMock;
    private IriConverterInterface|MockObject $iriConverterMock;

    public function setUp(): void {
        parent::setUp();
        $this->managerRegistryMock = $this->createStub(ManagerRegistry::class);
        $entityManagerMock = $this->createStub(EntityManager::class);
        $this->queryBuilderMock = $this->createMock(QueryBuilder::class);
        $this->queryNameGeneratorInterfaceMock = $this->createStub(QueryNameGeneratorInterface::class);
        $this->iriConverterMock = $this->createMock(IriConverterInterface::class);

        $entityManagerMock
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilderMock)
        ;

        $this->queryBuilderMock
            ->method('from')->willReturnSelf()
        ;

        $this->queryBuilderMock
            ->method('select')->willReturnSelf()
        ;

        $this->queryBuilderMock
            ->method('innerJoin')->willReturnSelf()
        ;

        $this->queryBuilderMock
            ->method('join')->willReturnSelf()
        ;

        $this->queryBuilderMock
            ->method('andWhere')->willReturnSelf()
        ;

        $this->queryBuilderMock
            ->method('getRootAliases')
            ->willReturn(['o'])
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

        $this->queryBuilderMock
            ->method('getEntityManager')
            ->willReturn($entityManagerMock)
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
            ContentNode::class,
            ChecklistNode::class,
            ColumnLayout::class,
            MaterialNode::class,
            MultiSelect::class,
            NoData::class,
            ResponsiveLayout::class,
            SingleText::class,
            Storyboard::class,
        ];

        foreach ($resourceClasses as $resourceClass) {
            $operations = (new \ReflectionClass($resourceClass))->getAttributes(ApiResource::class)[0]->newInstance()->getOperations();
            $mainCollection = array_values(array_filter(iterator_to_array($operations), fn ($operation) => $operation instanceof GetCollection))[0];
            $parameters = $mainCollection->getParameters();
            $parameter = $parameters instanceof Parameters ? $parameters->get('period') : $parameters['period'];

            $this->assertSame(ContentNodePeriodFilter::class, $parameter->getFilter(), $resourceClass);
        }
    }

    public function testFailsForResouceClassOtherThanContentNode() {
        // given
        $filter = new ContentNodePeriodFilter($this->iriConverterMock);

        // then
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('ContentNodePeriodFilter can only be applied to entities of type ContentNode (received: DummyClass).');

        // when
        $filter->apply($this->queryBuilderMock, $this->queryNameGeneratorInterfaceMock, 'DummyClass', null, ['parameter' => $this->parameter('/period/123')]);
    }

    public function testPropagatesUnresolvableIri() {
        // given
        $filter = new ContentNodePeriodFilter($this->iriConverterMock);
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
        $filter->apply($this->queryBuilderMock, $this->queryNameGeneratorInterfaceMock, ContentNode::class, null, ['parameter' => $this->parameter('/period/123')]);
    }

    public function testAddsFilterForPeriodProperty() {
        // given
        $filter = new ContentNodePeriodFilter($this->iriConverterMock);
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
            ->method('setParameter')
            ->with('period_a1', $period)
        ;

        $this->queryBuilderMock
            ->expects($this->exactly(1))
            ->method('innerJoin')
        ;

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('andWhere')
        ;

        // when
        $filter->apply($this->queryBuilderMock, $this->queryNameGeneratorInterfaceMock, ContentNode::class, null, ['parameter' => $this->parameter('/period/123')]);
    }

    private function parameter(string $value): QueryParameter {
        return (new QueryParameter(key: 'period'))->withValue($value);
    }
}
