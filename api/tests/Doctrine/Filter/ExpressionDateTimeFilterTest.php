<?php

namespace App\Tests\Doctrine\Filter;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\QueryParameter;
use ApiPlatform\OpenApi\Model\Parameter as OpenApiParameter;
use App\Doctrine\Filter\ExpressionDateTimeFilter;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @internal
 */
#[AllowMockObjectsWithoutExpectations]
class ExpressionDateTimeFilterTest extends TestCase {
    private ManagerRegistry|MockObject $managerRegistryMock;
    private MockObject|QueryBuilder $queryBuilderMock;
    private MockObject|QueryNameGeneratorInterface $queryNameGeneratorInterfaceMock;

    public function setUp(): void {
        parent::setUp();
        $this->managerRegistryMock = $this->createStub(ManagerRegistry::class);
        $this->queryBuilderMock = $this->createMock(QueryBuilder::class);
        $this->queryNameGeneratorInterfaceMock = $this->createStub(QueryNameGeneratorInterface::class);

        $this->queryBuilderMock
            ->method('getRootAliases')
            ->willReturn(['o'])
        ;
        $this->queryBuilderMock
            ->method('getDQLPart')
            ->willReturnCallback(function (string $part) {
                return 'join' === $part ? [] : null;
            })
        ;

        $this->queryNameGeneratorInterfaceMock
            ->method('generateParameterName')
            ->willReturnCallback(fn (string $field): string => $field.'_a1')
        ;
        $this->queryNameGeneratorInterfaceMock
            ->method('generateJoinAlias')
            ->willReturnCallback(fn (string $field): string => $field.'_j1')
        ;

        $classMetadata = $this->createStub(ClassMetadata::class);
        $objectManager = $this->createStub(ObjectManager::class);

        $this->managerRegistryMock->method('getManagerForClass')->willReturn($objectManager);
        $objectManager->method('getClassMetadata')->willReturn($classMetadata);
        $classMetadata->method('hasAssociation')->willReturn(true);
        $classMetadata->method('getAssociationTargetClass')->willReturn('');
    }

    public function testGetDescriptionDoesNothingWhenNoPropertiesDefined() {
        // given
        $filter = new ExpressionDateTimeFilter($this->managerRegistryMock);

        // when
        $description = $filter->getDescription('Dummy');

        // then
        $this->assertSame([], $description);
    }

    public function testGetOpenApiParametersDocumentsAllOperators() {
        // given
        $filter = new ExpressionDateTimeFilter($this->managerRegistryMock, null, ['incrementedSomething' => 'something + 1']);

        // when
        $parameters = $filter->getOpenApiParameters(new QueryParameter(key: 'incrementedSomething'));

        // then
        $this->assertSame(
            [
                'incrementedSomething[before]',
                'incrementedSomething[strictly_before]',
                'incrementedSomething[after]',
                'incrementedSomething[strictly_after]',
            ],
            array_map(fn (OpenApiParameter $parameter) => $parameter->getName(), $parameters)
        );
    }

    public function testGetDescriptionDisallowsEmptyExpression() {
        // given
        $filter = new ExpressionDateTimeFilter($this->managerRegistryMock, null, ['incrementedSomething' => '']);

        // when
        $description = $filter->getDescription('Dummy');

        // then
        $this->assertSame([], $description);
    }

    public function testApplyChecksForDefinedFilters() {
        // given
        $filter = new ExpressionDateTimeFilter($this->managerRegistryMock, null, [/* this array intentionally left blank */]);

        // then
        $this->queryBuilderMock
            ->expects($this->never())
            ->method('andWhere')
        ;

        // when
        $filter->apply($this->queryBuilderMock, $this->queryNameGeneratorInterfaceMock, 'Dummy', null, ['filters' => [
            'incrementedSomething' => ['before' => '2022-02-02'],
        ]]);
    }

    public function testApplyChecksForInvalidFilterState() {
        // given
        $filter = new ExpressionDateTimeFilter($this->managerRegistryMock, null, ['incrementedSomething' => '']);

        // then
        $this->queryBuilderMock
            ->expects($this->never())
            ->method('andWhere')
        ;

        // when
        $filter->apply($this->queryBuilderMock, $this->queryNameGeneratorInterfaceMock, 'Dummy', null, ['filters' => [
            'incrementedSomething' => null,
        ]]);
    }

    public function testApplyChecksForInvalidDate() {
        // given
        $filter = new ExpressionDateTimeFilter($this->managerRegistryMock, null, ['incrementedSomething' => '']);

        // then
        $this->queryBuilderMock
            ->expects($this->never())
            ->method('andWhere')
        ;

        // when
        $filter->apply($this->queryBuilderMock, $this->queryNameGeneratorInterfaceMock, 'Dummy', null, ['filters' => [
            'incrementedSomething' => ['before' => 'the beginning of all time'],
        ]]);
    }

    public function testApplyLogsInvalidDate() {
        // given
        $logger = $this->createMock(LoggerInterface::class);
        $filter = new ExpressionDateTimeFilter($this->managerRegistryMock, $logger, ['incrementedSomething' => 'something + 1']);

        // then
        $logger->expects($this->once())->method('notice')->with('Invalid filter ignored');

        // when
        $filter->apply($this->queryBuilderMock, $this->queryNameGeneratorInterfaceMock, 'Dummy', null, ['filters' => [
            'incrementedSomething' => ['before' => 'the beginning of all time'],
        ]]);
    }

    public function testHasLoggerOnlyWhenOneWasSet() {
        // given
        $filter = new ExpressionDateTimeFilter($this->managerRegistryMock);

        // then
        $this->assertFalse($filter->hasLogger());

        // when
        $filter->setLogger($this->createStub(LoggerInterface::class));

        // then
        $this->assertTrue($filter->hasLogger());
    }

    #[DataProvider('getOperators')]
    public function testApplyFiltersByExpression(string $filterOperator, string $operator) {
        // given
        $filter = new ExpressionDateTimeFilter($this->managerRegistryMock, null, ['incrementedSomething' => 'something + 1']);

        // then
        $this->queryBuilderMock
            ->expects($this->once())
            ->method('andWhere')
            ->with("(something + 1) {$operator} :incrementedSomething_a1")
        ;

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('setParameter')
            ->with('incrementedSomething_a1', new \DateTimeImmutable('2022-02-02'))
        ;

        // when
        $filter->apply($this->queryBuilderMock, $this->queryNameGeneratorInterfaceMock, 'Dummy', null, ['filters' => [
            'incrementedSomething' => [$filterOperator => '2022-02-02'],
        ]]);
    }

    #[DataProvider('getOperators')]
    public function testApplyReplacesSelfAlias(string $filterOperator, string $operator) {
        // given
        $filter = new ExpressionDateTimeFilter($this->managerRegistryMock, null, ['incrementedSomething' => '{}.something + 1']);

        // then
        $this->queryBuilderMock
            ->expects($this->once())
            ->method('andWhere')
            ->with("(o.something + 1) {$operator} :incrementedSomething_a1")
        ;

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('setParameter')
            ->with('incrementedSomething_a1', new \DateTimeImmutable('2022-02-02'))
        ;

        // when
        $filter->apply($this->queryBuilderMock, $this->queryNameGeneratorInterfaceMock, 'Dummy', null, ['filters' => [
            'incrementedSomething' => [$filterOperator => '2022-02-02'],
        ]]);
    }

    #[DataProvider('getOperators')]
    public function testApplyReplacesRelationAlias(string $filterOperator, string $operator) {
        // given
        $filter = new ExpressionDateTimeFilter($this->managerRegistryMock, null, ['incrementedSomething' => '{parent.something} + 1']);

        // then
        $this->queryBuilderMock
            ->expects($this->once())
            ->method('andWhere')
            ->with("(parent_j1.something + 1) {$operator} :incrementedSomething_a1")
        ;

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('setParameter')
            ->with('incrementedSomething_a1', new \DateTimeImmutable('2022-02-02'))
        ;

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('innerJoin')
            ->with('o.parent', 'parent_j1', null, null)
        ;

        // when
        $filter->apply($this->queryBuilderMock, $this->queryNameGeneratorInterfaceMock, 'Dummy', null, ['filters' => [
            'incrementedSomething' => [$filterOperator => '2022-02-02'],
        ]]);
    }

    #[DataProvider('getOperators')]
    public function testApplyReplacesMultipleRelationAliases(string $filterOperator, string $operator) {
        // given
        $filter = new ExpressionDateTimeFilter($this->managerRegistryMock, null, ['incrementedSomething' => '{}.something + {parent.something} + {parent2.something}']);

        // then
        $this->queryBuilderMock
            ->expects($this->once())
            ->method('andWhere')
            ->with("(o.something + parent_j1.something + parent2_j1.something) {$operator} :incrementedSomething_a1")
        ;

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('setParameter')
            ->with('incrementedSomething_a1', new \DateTimeImmutable('2022-02-02'))
        ;

        $this->queryBuilderMock
            ->expects($this->exactly(2))
            ->method('innerJoin')
        ;

        // when
        $filter->apply($this->queryBuilderMock, $this->queryNameGeneratorInterfaceMock, '', null, ['filters' => [
            'incrementedSomething' => [$filterOperator => '2022-02-02'],
        ]]);
    }

    #[DataProvider('getOperators')]
    public function testApplyReplacesMultipleInstancesOfTheSameRelationAlias(string $filterOperator, string $operator) {
        // given
        $filter = new ExpressionDateTimeFilter($this->managerRegistryMock, null, ['incrementedSomething' => '{parent.something} + {parent.something}']);

        // then
        $this->queryBuilderMock
            ->expects($this->once())
            ->method('andWhere')
            ->with("(parent_j1.something + parent_j1.something) {$operator} :incrementedSomething_a1")
        ;

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('setParameter')
            ->with('incrementedSomething_a1', new \DateTimeImmutable('2022-02-02'))
        ;

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('innerJoin')
            ->with('o.parent', 'parent_j1', null, null)
        ;

        // when
        $filter->apply($this->queryBuilderMock, $this->queryNameGeneratorInterfaceMock, 'Dummy', null, ['filters' => [
            'incrementedSomething' => [$filterOperator => '2022-02-02'],
        ]]);
    }

    public static function getOperators(): \Iterator {
        yield 'before' => ['before', '<='];

        yield 'strictly_before' => ['strictly_before', '<'];

        yield 'after' => ['after', '>='];

        yield 'strictly_after' => ['strictly_after', '>'];
    }
}
