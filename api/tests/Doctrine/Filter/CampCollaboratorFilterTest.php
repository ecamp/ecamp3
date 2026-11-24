<?php

namespace App\Tests\Doctrine\Filter;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Parameters;
use ApiPlatform\Metadata\QueryParameter;
use App\Doctrine\Filter\CampCollaboratorFilter;
use App\Entity\Camp;
use App\Entity\ContentNode;
use App\Entity\Period;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[AllowMockObjectsWithoutExpectations]
class CampCollaboratorFilterTest extends TestCase {
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
            ->method('select')->willReturnSelf()
        ;

        $this->queryBuilderMock
            ->method('from')->willReturnSelf()
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
            ->method('getDQLPart')
            ->willReturn([])
        ;

        $this->queryBuilderMock
            ->method('getEntityManager')
            ->willReturn($entityManagerMock)
        ;

        $this->queryNameGeneratorInterfaceMock
            ->method('generateJoinAlias')
            ->willReturnCallback(fn (string $field): string => $field.'_j1')
        ;
    }

    public function testIsDeclaredAsQueryParameterOnMainCollectionOperation() {
        $resourceClasses = [
            Camp::class,
            Period::class,
        ];

        foreach ($resourceClasses as $resourceClass) {
            $operations = (new \ReflectionClass($resourceClass))->getAttributes(ApiResource::class)[0]->newInstance()->getOperations();
            $mainCollection = array_values(array_filter(iterator_to_array($operations), fn ($operation) => $operation instanceof GetCollection))[0];
            $parameters = $mainCollection->getParameters();
            $parameter = $parameters instanceof Parameters ? $parameters->get('campCollaborator') : $parameters['campCollaborator'];

            $this->assertSame(CampCollaboratorFilter::class, $parameter->getFilter(), $resourceClass);
        }
    }

    public function testFailsForResouceClassNotImplementingBelongsToCampInterface() {
        // given
        $filter = new CampCollaboratorFilter($this->iriConverterMock);

        // then
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CampCollaboratorFilter can only be applied to entities which implement BelongsToCampInterface (received: App\Entity\User).');

        // when
        $filter->apply($this->queryBuilderMock, $this->queryNameGeneratorInterfaceMock, User::class, null, ['parameter' => $this->parameter('/users/123')]);
    }

    public function testPropagatesUnresolvableIri() {
        // given
        $filter = new CampCollaboratorFilter($this->iriConverterMock);
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
        $filter->apply($this->queryBuilderMock, $this->queryNameGeneratorInterfaceMock, ContentNode::class, null, ['parameter' => $this->parameter('/users/123')]);
    }

    public function testAddsFilterForPeriodProperty() {
        // given
        $filter = new CampCollaboratorFilter($this->iriConverterMock);
        $collaborator = new User();

        // then
        $this->iriConverterMock
            ->expects($this->once())
            ->method('getResourceFromIri')
            ->with('/users/123')
            ->willReturn($collaborator)
        ;

        $this->queryBuilderMock
            ->expects($this->exactly(2))
            ->method('setParameter')
        ;

        // when
        $filter->apply($this->queryBuilderMock, $this->queryNameGeneratorInterfaceMock, ContentNode::class, null, ['parameter' => $this->parameter('/users/123')]);
    }

    private function parameter(string $value): QueryParameter {
        return (new QueryParameter(key: 'campCollaborator'))->withValue($value);
    }
}
