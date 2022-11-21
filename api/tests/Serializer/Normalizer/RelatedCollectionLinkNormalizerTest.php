<?php

namespace App\Tests\Serializer\Normalizer;

use ApiPlatform\Api\FilterInterface;
use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operations;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;
use App\Metadata\Resource\Factory\UriTemplateFactory;
use App\Serializer\Normalizer\RelatedCollectionLink;
use App\Serializer\Normalizer\RelatedCollectionLinkNormalizer;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rize\UriTemplate;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\NameConverter\AdvancedNameConverterInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @internal
 */
class RelatedCollectionLinkNormalizerTest extends TestCase {
    private RelatedCollectionLinkNormalizer $normalizer;

    private MockObject|NormalizerInterface $decoratedMock;
    private MockObject|ServiceLocator $filterLocatorMock;
    private MockObject|NameConverterInterface $nameConverterMock;
    private MockObject|UriTemplate $uriTemplate;
    private MockObject|UriTemplateFactory $uriTemplateFactory;
    private MockObject|RouterInterface $routerMock;
    private IriConverterInterface|MockObject $iriConverterMock;
    private ManagerRegistry|MockObject $managerRegistryMock;
    private MockObject|ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactoryMock;
    private MockObject|PropertyAccessorInterface $propertyAccessor;

    private ?FilterInterface $filterInstance;

    protected function setUp(): void {
        $this->filterLocatorMock = $this->createMock(ServiceLocator::class);
        $this->filterLocatorMock->method('get')->willReturnCallback(function ($name) {
            return $this->filterInstance;
        });

        $this->decoratedMock = $this->createMock(NormalizerInterface::class);
        $this->nameConverterMock = $this->createMock(AdvancedNameConverterInterface::class);
        $this->uriTemplate = $this->createMock(UriTemplate::class);
        $this->uriTemplateFactory = $this->createMock(UriTemplateFactory::class);
        $this->routerMock = $this->createMock(RouterInterface::class);
        $this->iriConverterMock = $this->createMock(IriConverterInterface::class);
        $this->managerRegistryMock = $this->createMock(ManagerRegistry::class);
        $this->resourceMetadataCollectionFactoryMock = $this->createMock(ResourceMetadataCollectionFactoryInterface::class);
        $this->propertyAccessor = $this->createMock(PropertyAccessorInterface::class);

        $this->iriConverterMock->method('getIriFromResource')->willReturn('/iri');

        $this->normalizer = new RelatedCollectionLinkNormalizer(
            $this->decoratedMock,
            $this->filterLocatorMock,
            $this->nameConverterMock,
            $this->uriTemplate,
            $this->uriTemplateFactory,
            $this->routerMock,
            $this->iriConverterMock,
            $this->managerRegistryMock,
            $this->resourceMetadataCollectionFactoryMock,
            $this->propertyAccessor,
        );
        $this->normalizer->setSerializer($this->createMock(SerializerInterface::class));
    }

    public function testDelegatesSupportCheckToDecorated() {
        $this->decoratedMock
            ->expects($this->exactly(2))
            ->method('supportsNormalization')
            ->willReturnOnConsecutiveCalls(true, false)
        ;

        $this->assertTrue($this->normalizer->supportsNormalization([]));
        $this->assertFalse($this->normalizer->supportsNormalization([]));
    }

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
        $this->assertEquals($delegatedResult, $result);
    }

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
        $this->assertEquals($delegatedResult, $result);
    }

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
        $this->assertEquals($delegatedResult, $result);
    }

    public function testNormalizeReplacesLinkArrayWithSingleFilteredCollectionLink() {
        // given
        $resource = new ParentEntity();
        $this->mockDecoratedNormalizer();
        $this->mockNameConverter();
        $this->mockAssociationMetadata(['targetEntity' => Child::class, 'mappedBy' => 'parent']);
        $this->mockRelatedResourceMetadata(['filters' => ['attribute_filter_something_something']]);
        $this->mockRelatedFilterDescription(['parent' => ['strategy' => 'exact']]);
        $this->mockGeneratedRoute();

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => ParentEntity::class]);

        // then
        $this->shouldReplaceChildrenWithLink($result);
    }

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
            ->with('App\\Entity\\DummyEntity')
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
        $this->assertEquals([
            'hello' => 'world',
            '_links' => [
                'relatedEntities' => ['href' => '/relatedEntities?test_param=value'],
                'firstBorn' => ['href' => '/children/1'],
            ],
        ], $result);
    }

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

        $this->mockRelatedResourceMetadata(['filters' => ['attribute_filter_something_something']]);
        $this->mockRelatedFilterDescription(['parent' => ['strategy' => 'exact']]);
        $this->mockAssociationMetadata(['targetEntity' => Child::class, 'mappedBy' => 'parent']);
        $this->mockGeneratedRoute();

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => ParentEntity::class]);

        // then
        $this->assertEquals([
            'hello' => 'world',
            '_links' => [
                'childrenWithSerializedName' => ['href' => '/children?parent=%2Fparents%2F123'],
                'firstBorn' => ['href' => '/children/1'],
            ],
        ], $result);
    }

    public function testNormalizeDoesntReplaceWhenFilterDoesntApplyToMappedProperty() {
        // given
        $resource = new ParentEntity();
        $this->mockDecoratedNormalizer();
        $this->mockNameConverter();
        $this->mockAssociationMetadata(['targetEntity' => Child::class, 'mappedBy' => 'parent']);
        $this->mockRelatedResourceMetadata(['filters' => ['attribute_filter_something_something']]);
        $this->mockRelatedFilterDescription(['some_other_property' => ['strategy' => 'exact']]);
        $this->mockGeneratedRoute();

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => ParentEntity::class]);

        // then
        $this->shouldNotReplaceChildren($result);
    }

    public function testNormalizeDoesntReplaceWhenEmptyFiltersArray() {
        // given
        $resource = new ParentEntity();
        $this->mockDecoratedNormalizer();
        $this->mockNameConverter();
        $this->mockAssociationMetadata(['targetEntity' => Child::class, 'mappedBy' => 'parent']);
        $this->mockRelatedResourceMetadata(['filters' => []]);
        $this->mockRelatedFilterDescription(['parent' => ['strategy' => 'exact']]);
        $this->mockGeneratedRoute();

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => ParentEntity::class]);

        // then
        $this->shouldNotReplaceChildren($result);
    }

    public function testNormalizeDoesntReplaceWhenNoFilters() {
        // given
        $resource = new ParentEntity();
        $this->mockDecoratedNormalizer();
        $this->mockNameConverter();
        $this->mockAssociationMetadata(['targetEntity' => Child::class, 'mappedBy' => 'parent']);
        $this->mockRelatedResourceMetadata([]);
        $this->mockRelatedFilterDescription(['parent' => ['strategy' => 'exact']]);
        $this->mockGeneratedRoute();

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => ParentEntity::class]);

        // then
        $this->shouldNotReplaceChildren($result);
    }

    public function testNormalizeDoesntReplaceWhenTargetEntityIsMissing() {
        // given
        $resource = new ParentEntity();
        $this->mockDecoratedNormalizer();
        $this->mockNameConverter();
        $this->mockAssociationMetadata(['targetEntity' => null, 'mappedBy' => 'parent']);
        $this->mockRelatedResourceMetadata(['filters' => ['attribute_filter_something_something']]);
        $this->mockRelatedFilterDescription(['parent' => ['strategy' => 'exact']]);
        $this->mockGeneratedRoute();

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => ParentEntity::class]);

        // then
        $this->shouldNotReplaceChildren($result);
    }

    public function testNormalizeDoesntReplaceWhenNotADoctrineAssociation() {
        // given
        $resource = new ParentEntity();
        $this->mockDecoratedNormalizer();
        $this->mockNameConverter();

        $classMetadata = $this->createMock(ORM\ClassMetadata::class);
        $classMetadata->method('getAssociationMapping')->willThrowException(new ORM\MappingException('test exception'));
        $manager = $this->createMock(EntityManagerInterface::class);
        $manager->method('getClassMetadata')->willReturn($classMetadata);
        $this->managerRegistryMock->method('getManagerForClass')->willReturn($manager);

        $this->mockRelatedResourceMetadata(['filters' => ['attribute_filter_something_something']]);
        $this->mockRelatedFilterDescription(['parent' => ['strategy' => 'exact']]);
        $this->mockGeneratedRoute();

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => ParentEntity::class]);

        // then
        $this->shouldNotReplaceChildren($result);
    }

    public function testNormalizeDoesntReplaceWhenMappedByIsMissing() {
        // given
        $resource = new ParentEntity();
        $this->mockDecoratedNormalizer();
        $this->mockNameConverter();
        $this->mockAssociationMetadata(['targetEntity' => Child::class, 'mappedBy' => null, 'inversedBy' => null]);
        $this->mockRelatedResourceMetadata(['filters' => ['attribute_filter_something_something']]);
        $this->mockRelatedFilterDescription(['parent' => ['strategy' => 'exact']]);
        $this->mockGeneratedRoute();

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => ParentEntity::class]);

        // then
        $this->shouldNotReplaceChildren($result);
    }

    public function testNormalizeDoesntReplaceWhenFilterDoesntExistInContainer() {
        // given
        $resource = new ParentEntity();
        $this->mockDecoratedNormalizer();
        $this->mockNameConverter();
        $this->mockAssociationMetadata(['targetEntity' => Child::class, 'mappedBy' => 'parent']);
        $this->mockRelatedResourceMetadata(['filters' => ['attribute_filter_something_something']]);
        $this->filterInstance = null;
        $this->mockGeneratedRoute();

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => ParentEntity::class]);

        // then
        $this->shouldNotReplaceChildren($result);
    }

    public function testNormalizeDoesntReplaceWhenFilterIsNotSearchFilter() {
        // given
        $resource = new ParentEntity();
        $this->mockDecoratedNormalizer();
        $this->mockNameConverter();
        $this->mockAssociationMetadata(['targetEntity' => Child::class, 'mappedBy' => 'parent']);
        $this->mockRelatedResourceMetadata(['filters' => ['attribute_filter_something_something']]);
        $this->filterInstance = $this->createMock(DateFilter::class);
        $this->filterInstance->method('getDescription')->willReturn(['filters' => ['attribute_filter_something_something']]);
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
        $classMetadata = $this->createMock(ORM\ClassMetadata::class);
        $classMetadata->method('getAssociationMapping')->willReturn($relationMetadata);
        $classMetadata->method('hasAssociation')->willReturn(true);

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager->method('getClassMetadata')->willReturn($classMetadata);

        $this->managerRegistryMock->method('getManagerForClass')->willReturn($manager);
    }

    protected function mockRelatedResourceMetadata($collectionOperationMetadata) {
        $collectionOperationMetadata['name'] = '_api_/dummys{._format}_get_collection';

        $metadataCollection = new ResourceMetadataCollection('Dummy');
        $metadataCollection->append((new ApiResource())->withOperations(new Operations([
            new GetCollection(
                ...$collectionOperationMetadata
            ),
        ])));

        $this->resourceMetadataCollectionFactoryMock->method('create')->willReturn($metadataCollection);
    }

    protected function mockNameConverter() {
        $this->nameConverterMock->method('denormalize')->willReturnArgument(0);
    }

    protected function mockRelatedFilterDescription($properties) {
        $this->filterInstance = new SearchFilter($this->managerRegistryMock, $this->iriConverterMock, null, null, $properties);
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
    private string $id = '123';

    private string $hello = 'world';

    #[ORM\OneToMany(targetEntity: Child::class, mappedBy: 'parent')]
    private Collection $children;

    #[ORM\OneToOne(targetEntity: Child::class)]
    private ?Child $firstBorn;

    #[SerializedName('childrenWithSerializedName')]
    #[ORM\OneToMany(targetEntity: Child::class, mappedBy: 'parent')]
    private Collection $renamedChildren;

    public function getFilterValue(): string {
        return '';
    }

    #[RelatedCollectionLink('App\\Entity\\DummyEntity', ['test_param' => 'filterValue'])]
    public function getRelatedEntities(): array {
        return [];
    }
}

#[ApiFilter(SearchFilter::class, properties: ['parent'])]
class Child {
    #[ORM\ManyToOne(targetEntity: ParentEntity::class, inversedBy: 'children')]
    private ?ParentEntity $parent;
}
