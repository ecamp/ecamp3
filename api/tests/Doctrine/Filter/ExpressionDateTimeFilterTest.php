<?php

namespace App\Tests\Doctrine\Filter;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Doctrine\Filter\ExpressionDateTimeFilter;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ExpressionDateTimeFilterTest extends TestCase {
    private MockObject|ManagerRegistry $managerRegistryMock;
    private MockObject|QueryBuilder $queryBuilderMock;
    private MockObject|QueryNameGeneratorInterface $queryNameGeneratorInterfaceMock;

    public function setUp(): void {
        parent::setUp();
        $this->managerRegistryMock = $this->createMock(ManagerRegistry::class);
        $this->queryBuilderMock = $this->createMock(QueryBuilder::class);
        $this->queryNameGeneratorInterfaceMock = $this->createMock(QueryNameGeneratorInterface::class);

        $this->queryBuilderMock
            ->method('getRootAliases')
            ->willReturn(['o'])
        ;
        $this->queryBuilderMock
            ->method('getDQLPart')
            ->with('join')
            ->willReturn([])
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

    public function testGetDescriptionDoesNothingWhenNoPropertiesDefined() {
        // given
        $filter = new ExpressionDateTimeFilter($this->managerRegistryMock, null, null, null);

        // when
        $description = $filter->getDescription('Dummy');

        // then
        $this->assertEquals([], $description);
    }

    public function testGetDescription() {
        // given
        $filter = new ExpressionDateTimeFilter($this->managerRegistryMock, null, null, ['incrementedSomething' => 'something + 1']);

        // when
        $description = $filter->getDescription('Dummy');

        // then
        $this->assertEquals([
            'incrementedSomething[before]' => [
                'property' => 'incrementedSomething',
                'type' => 'DateTimeInterface',
                'required' => false,
            ],
            'incrementedSomething[strictly_before]' => [
                'property' => 'incrementedSomething',
                'type' => 'DateTimeInterface',
                'required' => false,
            ],
            'incrementedSomething[after]' => [
                'property' => 'incrementedSomething',
                'type' => 'DateTimeInterface',
                'required' => false,
            ],
            'incrementedSomething[strictly_after]' => [
                'property' => 'incrementedSomething',
                'type' => 'DateTimeInterface',
                'required' => false,
            ],
        ], $description);
    }

    public function testGetDescriptionDisallowsEmptyExpression() {
        // given
        $filter = new ExpressionDateTimeFilter($this->managerRegistryMock, null, null, ['incrementedSomething' => '']);

        // when
        $description = $filter->getDescription('Dummy');

        // then
        $this->assertEquals([], $description);
    }

    public function testApplyChecksForDefinedFilters() {
        // given
        $filter = new ExpressionDateTimeFilter($this->managerRegistryMock, null, null, [/* this array intentionally left blank */]);

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
        $filter = new ExpressionDateTimeFilter($this->managerRegistryMock, null, null, ['incrementedSomething' => '']);

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
        $filter = new ExpressionDateTimeFilter($this->managerRegistryMock, null, null, ['incrementedSomething' => '']);

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

    /**
     * @dataProvider getOperators
     */
    public function testApplyFiltersByExpression($filterOperator, $operator) {
        // given
        $filter = new ExpressionDateTimeFilter($this->managerRegistryMock, null, null, ['incrementedSomething' => 'something + 1']);

        // then
        $this->queryBuilderMock
            ->expects($this->once())
            ->method('andWhere')
            ->with("(something + 1) {$operator} :incrementedSomething_a1")
        ;

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('setParameter')
            ->with('incrementedSomething_a1', new \DateTime('2022-02-02'))
        ;

        // when
        $filter->apply($this->queryBuilderMock, $this->queryNameGeneratorInterfaceMock, 'Dummy', null, ['filters' => [
            'incrementedSomething' => [$filterOperator => '2022-02-02'],
        ]]);
    }

    /**
     * @dataProvider getOperators
     */
    public function testApplyReplacesSelfAlias($filterOperator, $operator) {
        // given
        $filter = new ExpressionDateTimeFilter($this->managerRegistryMock, null, null, ['incrementedSomething' => '{}.something + 1']);

        // then
        $this->queryBuilderMock
            ->expects($this->once())
            ->method('andWhere')
            ->with("(o.something + 1) {$operator} :incrementedSomething_a1")
        ;

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('setParameter')
            ->with('incrementedSomething_a1', new \DateTime('2022-02-02'))
        ;

        // when
        $filter->apply($this->queryBuilderMock, $this->queryNameGeneratorInterfaceMock, 'Dummy', null, ['filters' => [
            'incrementedSomething' => [$filterOperator => '2022-02-02'],
        ]]);
    }

    /**
     * @dataProvider getOperators
     */
    public function testApplyReplacesRelationAlias($filterOperator, $operator) {
        // given
        $filter = new ExpressionDateTimeFilter($this->managerRegistryMock, null, null, ['incrementedSomething' => '{parent.something} + 1']);

        // then
        $this->queryBuilderMock
            ->expects($this->once())
            ->method('andWhere')
            ->with("(parent_j1.something + 1) {$operator} :incrementedSomething_a1")
        ;

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('setParameter')
            ->with('incrementedSomething_a1', new \DateTime('2022-02-02'))
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

    /**
     * @dataProvider getOperators
     */
    public function testApplyReplacesMultipleRelationAliases($filterOperator, $operator) {
        // given
        $filter = new ExpressionDateTimeFilter($this->managerRegistryMock, null, null, ['incrementedSomething' => '{}.something + {parent.something} + {parent2.something}']);

        // then
        $this->queryBuilderMock
            ->expects($this->once())
            ->method('andWhere')
            ->with("(o.something + parent_j1.something + parent2_j1.something) {$operator} :incrementedSomething_a1")
        ;

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('setParameter')
            ->with('incrementedSomething_a1', new \DateTime('2022-02-02'))
        ;

        $this->queryBuilderMock
            ->expects($this->exactly(2))
            ->method('innerJoin')
        ;

        // when
        $filter->apply($this->queryBuilderMock, $this->queryNameGeneratorInterfaceMock, 'Dummy', null, ['filters' => [
            'incrementedSomething' => [$filterOperator => '2022-02-02'],
        ]]);
    }

    /**
     * @dataProvider getOperators
     */
    public function testApplyReplacesMultipleInstancesOfTheSameRelationAlias($filterOperator, $operator) {
        // given
        $filter = new ExpressionDateTimeFilter($this->managerRegistryMock, null, null, ['incrementedSomething' => '{parent.something} + {parent.something}']);

        // then
        $this->queryBuilderMock
            ->expects($this->once())
            ->method('andWhere')
            ->with("(parent_j1.something + parent_j1.something) {$operator} :incrementedSomething_a1")
        ;

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('setParameter')
            ->with('incrementedSomething_a1', new \DateTime('2022-02-02'))
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

    public function getOperators() {
        return [
            'before' => ['before', '<='],
            'strictly_before' => ['strictly_before', '<'],
            'after' => ['after', '>='],
            'strictly_after' => ['strictly_after', '>'],
        ];
    }
}
