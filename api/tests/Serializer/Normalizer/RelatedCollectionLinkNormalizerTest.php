<?php

namespace App\Tests\Serializer\Normalizer;

use ApiPlatform\Doctrine\Orm\Filter\ExactFilter;
use ApiPlatform\Doctrine\Orm\Filter\IriFilter;
use ApiPlatform\Doctrine\Orm\Filter\PartialSearchFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Operations;
use ApiPlatform\Metadata\Parameters;
use ApiPlatform\Metadata\QueryParameter;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;
use App\Metadata\Resource\Factory\UriTemplateFactory;
use App\Serializer\Normalizer\RelatedCollectionLink;
use App\Serializer\Normalizer\RelatedCollectionLinkNormalizer;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToManyAssociationMapping;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Rize\UriTemplate;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @internal
 */
class RelatedCollectionLinkNormalizerTest extends TestCase {
    private RelatedCollectionLinkNormalizer $normalizer;

    private MockObject|NormalizerInterface $decoratedMock;
    private NameConverterInterface|Stub $nameConverterMock;
    private MockObject|UriTemplate $uriTemplate;
    private MockObject|UriTemplateFactory $uriTemplateFactory;
    private RouterInterface|Stub $routerMock;
    private IriConverterInterface|Stub $iriConverterMock;
    private ManagerRegistry|Stub $managerRegistryMock;
    private ResourceMetadataCollectionFactoryInterface|Stub $resourceMetadataCollectionFactoryMock;
    private PropertyAccessorInterface|Stub $propertyAccessor;
    private EntityManagerInterface|Stub $entityManager;

    protected function setUp(): void {
        $this->decoratedMock = $this->createMock(NormalizerInterface::class);
        $this->nameConverterMock = $this->createStub(NameConverterInterface::class);
        $this->uriTemplate = $this->createMock(UriTemplate::class);
        $this->uriTemplateFactory = $this->createMock(UriTemplateFactory::class);
        $this->routerMock = $this->createStub(RouterInterface::class);
        $this->iriConverterMock = $this->createStub(IriConverterInterface::class);
        $this->managerRegistryMock = $this->createStub(ManagerRegistry::class);
        $this->resourceMetadataCollectionFactoryMock = $this->createStub(ResourceMetadataCollectionFactoryInterface::class);
        $this->propertyAccessor = $this->createStub(PropertyAccessorInterface::class);

        $this->iriConverterMock->method('getIriFromResource')->willReturn('/iri');

        $this->entityManager = $this->createStub(EntityManagerInterface::class);

        $this->normalizer = new RelatedCollectionLinkNormalizer(
            $this->decoratedMock,
            $this->nameConverterMock,
            $this->uriTemplate,
            $this->uriTemplateFactory,
            $this->routerMock,
            $this->iriConverterMock,
            $this->entityManager,
            $this->resourceMetadataCollectionFactoryMock,
            $this->propertyAccessor,
        );
        $this->normalizer->setSerializer($this->createStub(SerializerInterface::class));
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testDelegatesSupportCheckToDecorated() {
        $this->decoratedMock
            ->expects($this->exactly(2))
            ->method('supportsNormalization')
            ->willReturnOnConsecutiveCalls(true, false)
        ;

        $this->assertTrue($this->normalizer->supportsNormalization([]));
        $this->assertFalse($this->normalizer->supportsNormalization([]));
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testDelegatesNormalizeToDecorated() {
        // given
        $resource = new ParentEntity();
        $delegatedResult = [
            'hello' => 'world',
            '_links' => [
                'firstBorn' => ['href' => '/children/1'],
            ],
        ];
        $this->decoratedMock->expects($this->once())
            ->method('normalize')
            ->willReturn($delegatedResult)
        ;

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => ParentEntity::class]);

        // then
        $this->assertSame($delegatedResult, $result);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testHandlesDecoratedNormalizerReturningAnIRIString() {
        // given
        $resource = new ParentEntity();
        $delegatedResult = '/parents/555';
        $this->decoratedMock->expects($this->once())
            ->method('normalize')
            ->willReturn($delegatedResult)
        ;

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => ParentEntity::class]);

        // then
        $this->assertSame($delegatedResult, $result);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testFallsBackToObjectClassWhenResourceClassIsMissingInContext() {
        // given
        $resource = new ParentEntity();
        $delegatedResult = [
            'hello' => 'world',
            '_links' => [
                'firstBorn' => ['href' => '/children/1'],
            ],
        ];
        $this->decoratedMock->expects($this->once())
            ->method('normalize')
            ->willReturn($delegatedResult)
        ;

        // when
        $result = $this->normalizer->normalize($resource, null, []);

        // then
        $this->assertSame($delegatedResult, $result);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testNormalizeReplacesLinkArrayWithSingleFilteredCollectionLink() {
        // given
        $resource = new ParentEntity();
        $this->mockDecoratedNormalizer();
        $this->mockNameConverter();
        $this->mockAssociationMetadata(OneToManyAssociationMapping::fromMappingArray([
            'targetEntity' => Child::class,
            'mappedBy' => 'parent',
            'fieldName' => 'children',
            'sourceEntity' => ParentEntity::class,
        ]));

        $this->mockRelatedResourceMetadata(['parameters' => ['parent' => new QueryParameter(filter: new IriFilter())]]);
        $this->mockGeneratedRoute();

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => ParentEntity::class]);

        // then
        $this->shouldReplaceChildrenWithLink($result);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testNormalizeReplacesLinkArrayWithSingleFilteredCollectionLinkBasedOnAttribute() {
        // given
        $resource = new ParentEntity();
        $this->decoratedMock->method('normalize')->willReturn([
            'hello' => 'world',
            '_links' => [
                'relatedEntities' => [
                    ['href' => '/schedule_entries/1'],
                    ['href' => '/schedule_entries/2'],
                ],
                'firstBorn' => ['href' => '/children/1'],
            ],
        ]);
        $this->mockNameConverter();
        $this->propertyAccessor->method('getValue')->willReturn('value');
        $this->uriTemplateFactory
            ->expects($this->once())
            ->method('createFromResourceClass')
            ->with('App\Entity\DummyEntity')
            ->willReturn(['/relatedEntities{/id}{?test_param}', true])
        ;
        $this->uriTemplate
            ->expects($this->once())
            ->method('expand')
            ->with('/relatedEntities{/id}{?test_param}', ['test_param' => 'value'])
            ->willReturn('/relatedEntities?test_param=value')
        ;

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => ParentEntity::class]);

        // then
        $this->assertSame([
            'hello' => 'world',
            '_links' => [
                'relatedEntities' => ['href' => '/relatedEntities?test_param=value'],
                'firstBorn' => ['href' => '/children/1'],
            ],
        ], $result);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testNormalizeReplacesSerializedNameLinkArray() {
        // given
        $resource = new ParentEntity();

        $this->decoratedMock->method('normalize')->willReturn([
            'hello' => 'world',
            '_links' => [
                'childrenWithSerializedName' => [
                    ['href' => '/children/1'],
                    ['href' => '/children/2'],
                ],
                'firstBorn' => ['href' => '/children/1'],
            ],
        ]);

        $this->nameConverterMock->method('denormalize')->willReturn('renamedChildren');

        $this->mockRelatedResourceMetadata(['parameters' => ['parent' => new QueryParameter(filter: new IriFilter())]]);
        $this->mockAssociationMetadata(OneToManyAssociationMapping::fromMappingArray([
            'targetEntity' => Child::class,
            'mappedBy' => 'parent',
            'fieldName' => 'children',
            'sourceEntity' => ParentEntity::class,
        ]));
        $this->mockGeneratedRoute();

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => ParentEntity::class]);

        // then
        $this->assertSame([
            'hello' => 'world',
            '_links' => [
                'childrenWithSerializedName' => ['href' => '/children?parent=%2Fparents%2F123'],
                'firstBorn' => ['href' => '/children/1'],
            ],
        ], $result);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testNormalizeDoesntReplaceWhenFilterDoesntApplyToMappedProperty() {
        // given
        $resource = new ParentEntity();
        $this->mockDecoratedNormalizer();
        $this->mockNameConverter();
        $this->mockAssociationMetadata(OneToManyAssociationMapping::fromMappingArray([
            'targetEntity' => Child::class,
            'mappedBy' => 'parent',
            'fieldName' => 'children',
            'sourceEntity' => ParentEntity::class,
        ]));
        $this->mockRelatedResourceMetadata(['parameters' => ['some_other_property' => new QueryParameter(filter: new IriFilter())]]);
        $this->mockGeneratedRoute();

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => ParentEntity::class]);

        // then
        $this->shouldNotReplaceChildren($result);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testNormalizeDoesntReplaceWhenEmptyFiltersArray() {
        // given
        $resource = new ParentEntity();
        $this->mockDecoratedNormalizer();
        $this->mockNameConverter();
        $this->mockAssociationMetadata(OneToManyAssociationMapping::fromMappingArray([
            'targetEntity' => Child::class,
            'mappedBy' => 'parent',
            'fieldName' => 'children',
            'sourceEntity' => ParentEntity::class,
        ]));
        $this->mockRelatedResourceMetadata(['parameters' => new Parameters()]);
        $this->mockGeneratedRoute();

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => ParentEntity::class]);

        // then
        $this->shouldNotReplaceChildren($result);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testNormalizeDoesntReplaceWhenNoFilters() {
        // given
        $resource = new ParentEntity();
        $this->mockDecoratedNormalizer();
        $this->mockNameConverter();
        $this->mockAssociationMetadata(OneToManyAssociationMapping::fromMappingArray([
            'targetEntity' => Child::class,
            'mappedBy' => 'parent',
            'fieldName' => 'children',
            'sourceEntity' => ParentEntity::class,
        ]));
        $this->mockRelatedResourceMetadata([]);
        $this->mockGeneratedRoute();

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => ParentEntity::class]);

        // then
        $this->shouldNotReplaceChildren($result);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testNormalizeDoesntReplaceWhenTargetEntityIsMissing() {
        // given
        $resource = new ParentEntity();
        $this->mockDecoratedNormalizer();
        $this->mockNameConverter();
        $this->mockAssociationMetadata(OneToManyAssociationMapping::fromMappingArray([
            'targetEntity' => '',
            'mappedBy' => 'parent',
            'fieldName' => 'children',
            'sourceEntity' => ParentEntity::class,
        ]));
        $this->mockRelatedResourceMetadata(['parameters' => ['parent' => new QueryParameter(filter: new IriFilter())]]);
        $this->mockGeneratedRoute();

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => ParentEntity::class]);

        // then
        $this->shouldNotReplaceChildren($result);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testNormalizeDoesntReplaceWhenNotADoctrineAssociation() {
        // given
        $resource = new ParentEntity();
        $this->mockDecoratedNormalizer();
        $this->mockNameConverter();

        $classMetadata = $this->createStub(ORM\ClassMetadata::class);
        $classMetadata->method('getAssociationMapping')->willThrowException(new ORM\MappingException('test exception'));
        $this->entityManager->method('getClassMetadata')->willReturn($classMetadata);
        $this->managerRegistryMock->method('getManagerForClass')->willReturn($this->entityManager);

        $this->mockRelatedResourceMetadata(['parameters' => ['parent' => new QueryParameter(filter: new IriFilter())]]);
        $this->mockGeneratedRoute();

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => ParentEntity::class]);

        // then
        $this->shouldNotReplaceChildren($result);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testNormalizeDoesntReplaceWhenMappedByIsMissing() {
        // given
        $resource = new ParentEntity();
        $this->mockDecoratedNormalizer();
        $this->mockNameConverter();
        $this->mockAssociationMetadata(OneToManyAssociationMapping::fromMappingArray([
            'targetEntity' => Child::class,
            'fieldName' => 'children',
            'sourceEntity' => ParentEntity::class,
        ]));
        $this->mockRelatedResourceMetadata(['parameters' => ['parent' => new QueryParameter(filter: new IriFilter())]]);
        $this->mockGeneratedRoute();

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => ParentEntity::class]);

        // then
        $this->shouldNotReplaceChildren($result);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testNormalizeReplacesLinkArrayWhenParameterUsesExactFilter() {
        // given
        $resource = new ParentEntity();
        $this->mockDecoratedNormalizer();
        $this->mockNameConverter();
        $this->mockAssociationMetadata(OneToManyAssociationMapping::fromMappingArray([
            'targetEntity' => Child::class,
            'mappedBy' => 'parent',
            'fieldName' => 'children',
            'sourceEntity' => ParentEntity::class,
        ]));
        $this->mockRelatedResourceMetadata(['parameters' => ['parent' => new QueryParameter(filter: new ExactFilter())]]);
        $this->mockGeneratedRoute();

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => ParentEntity::class]);

        // then
        $this->shouldReplaceChildrenWithLink($result);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testNormalizeDoesntReplaceWhenFilterIsNeitherIriNorExactFilter() {
        // given
        $resource = new ParentEntity();
        $this->mockDecoratedNormalizer();
        $this->mockNameConverter();
        $this->mockAssociationMetadata(OneToManyAssociationMapping::fromMappingArray([
            'targetEntity' => Child::class,
            'mappedBy' => 'parent',
            'fieldName' => 'children',
            'sourceEntity' => ParentEntity::class,
        ]));
        $this->mockRelatedResourceMetadata(['parameters' => ['parent' => new QueryParameter(filter: new PartialSearchFilter())]]);
        $this->mockGeneratedRoute();

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => ParentEntity::class]);

        // then
        $this->shouldNotReplaceChildren($result);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testNormalizeDoesntReplaceWhenParameterKeyDiffersFromRelationProperty() {
        // given
        $resource = new ParentEntity();
        $this->mockDecoratedNormalizer();
        $this->mockNameConverter();
        $this->mockAssociationMetadata(OneToManyAssociationMapping::fromMappingArray([
            'targetEntity' => Child::class,
            'mappedBy' => 'parent',
            'fieldName' => 'children',
            'sourceEntity' => ParentEntity::class,
        ]));
        $this->mockRelatedResourceMetadata(['parameters' => ['parent_filter' => new QueryParameter(filter: new IriFilter(), property: 'parent')]]);
        $this->mockGeneratedRoute();

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => ParentEntity::class]);

        // then
        $this->shouldNotReplaceChildren($result);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testNormalizeDoesntReplaceWhenParameterFiltersADifferentProperty() {
        // given
        $resource = new ParentEntity();
        $this->mockDecoratedNormalizer();
        $this->mockNameConverter();
        $this->mockAssociationMetadata(OneToManyAssociationMapping::fromMappingArray([
            'targetEntity' => Child::class,
            'mappedBy' => 'parent',
            'fieldName' => 'children',
            'sourceEntity' => ParentEntity::class,
        ]));
        $this->mockRelatedResourceMetadata(['parameters' => ['parent' => new QueryParameter(filter: new IriFilter(), property: 'other')]]);
        $this->mockGeneratedRoute();

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => ParentEntity::class]);

        // then
        $this->shouldNotReplaceChildren($result);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testNormalizeDoesntReplaceWhenMissingGetCollectionOperation() {
        // given
        $resource = new ParentEntity();
        $this->mockDecoratedNormalizer();
        $this->mockNameConverter();
        $this->mockAssociationMetadata(OneToManyAssociationMapping::fromMappingArray([
            'targetEntity' => Child::class,
            'mappedBy' => 'parent',
            'fieldName' => 'children',
            'sourceEntity' => ParentEntity::class,
        ]));

        $metadataCollection = new ResourceMetadataCollection('Dummy');
        $metadataCollection->append(new ApiResource()->withOperations(new Operations([new Get()])));
        $this->resourceMetadataCollectionFactoryMock->method('create')->willReturn($metadataCollection);

        $this->mockGeneratedRoute();

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => ParentEntity::class]);

        // then
        $this->shouldNotReplaceChildren($result);
    }

    protected function mockDecoratedNormalizer() {
        $this->decoratedMock->method('normalize')->willReturn([
            'hello' => 'world',
            '_links' => [
                'children' => [
                    ['href' => '/children/1'],
                    ['href' => '/children/2'],
                ],
                'firstBorn' => ['href' => '/children/1'],
            ],
        ]);
    }

    protected function mockAssociationMetadata($relationMetadata) {
        $classMetadata = $this->createStub(ORM\ClassMetadata::class);
        $classMetadata->method('getAssociationMapping')->willReturn($relationMetadata);
        $classMetadata->method('hasAssociation')->willReturn(true);

        $this->entityManager->method('getClassMetadata')->willReturn($classMetadata);
        $this->managerRegistryMock->method('getManagerForClass')->willReturn($this->entityManager);
    }

    protected function mockRelatedResourceMetadata($collectionOperationMetadata) {
        $collectionOperationMetadata['name'] = '_api_/dummys{._format}_get_collection';

        $metadataCollection = new ResourceMetadataCollection('Dummy');
        $metadataCollection->append(new ApiResource()->withOperations(new Operations([
            new GetCollection(
                ...$collectionOperationMetadata
            ),
        ])));

        $this->resourceMetadataCollectionFactoryMock->method('create')->willReturn($metadataCollection);
    }

    protected function mockNameConverter() {
        $this->nameConverterMock->method('denormalize')->willReturnArgument(0);
    }

    protected function shouldReplaceChildrenWithLink($result, $link = '/children?parent=%2Fparents%2F123') {
        $this->assertEquals([
            'hello' => 'world',
            '_links' => [
                'children' => ['href' => $link],
                'firstBorn' => ['href' => '/children/1'],
            ],
        ], $result);
    }

    protected function shouldNotReplaceChildren($result) {
        $this->assertEquals([
            'hello' => 'world',
            '_links' => [
                'children' => [
                    ['href' => '/children/1'],
                    ['href' => '/children/2'],
                ],
                'firstBorn' => ['href' => '/children/1'],
            ],
        ], $result);
    }

    protected function mockGeneratedRoute($generated = '/children?parent=%2Fparents%2F123') {
        $this->routerMock->method('generate')->willReturn($generated);
    }
}

class ParentEntity {
    #[ORM\OneToMany(targetEntity: Child::class, mappedBy: 'parent')]
    private readonly Collection $children;

    #[ORM\OneToOne(targetEntity: Child::class)]
    private ?Child $firstBorn = null;

    #[SerializedName('childrenWithSerializedName')]
    #[ORM\OneToMany(targetEntity: Child::class, mappedBy: 'parent')]
    private readonly Collection $renamedChildren;

    public function getFilterValue(): string {
        return '';
    }

    #[RelatedCollectionLink('App\Entity\DummyEntity', ['test_param' => 'filterValue'])]
    public function getRelatedEntities(): array {
        return [];
    }
}

class Child {
    #[ORM\ManyToOne(targetEntity: ParentEntity::class, inversedBy: 'children')]
    private ?ParentEntity $parent = null;
}
