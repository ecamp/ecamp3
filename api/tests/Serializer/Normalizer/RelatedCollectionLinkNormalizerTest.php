<?php

namespace App\Tests\Serializer\Normalizer;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Api\FilterInterface;
use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Symfony\Routing\RouteNameResolverInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use App\Serializer\Normalizer\RelatedCollectionLinkNormalizer;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @internal
 */
class RelatedCollectionLinkNormalizerTest extends TestCase {
    private RelatedCollectionLinkNormalizer $normalizer;

    /**
     * @var MockObject|NormalizerInterface
     */
    private $decoratedMock;

    /**
     * @var MockObject|ServiceLocator
     */
    private $filterLocatorMock;

    /**
     * @var MockObject|RouterInterface
     */
    private $routerMock;

    /**
     * @var IriConverterInterface|MockObject
     */
    private $iriConverterMock;

    /**
     * @var ManagerRegistry|MockObject
     */
    private $managerRegistryMock;

    /**
     * @var MockObject|ResourceMetadataFactoryInterface
     */
    private $resourceMetadataFactoryMock;

    /**
     * @var FilterInterface
     */
    private ?FilterInterface $filterInstance;

    protected function setUp(): void {
        $this->filterLocatorMock = $this->createMock(ServiceLocator::class);
        $this->filterLocatorMock->method('get')->willReturnCallback(function ($name) {
            return $this->filterInstance;
        });

        $this->decoratedMock = $this->createMock(ContextAwareNormalizerInterface::class);
        $this->routerMock = $this->createMock(RouterInterface::class);
        $this->iriConverterMock = $this->createMock(IriConverterInterface::class);
        $this->managerRegistryMock = $this->createMock(ManagerRegistry::class);
        $this->resourceMetadataFactoryMock = $this->createMock(ResourceMetadataFactoryInterface::class);

        $this->normalizer = new RelatedCollectionLinkNormalizer(
            $this->decoratedMock,
            $this->createMock(RouteNameResolverInterface::class),
            $this->filterLocatorMock,
            $this->routerMock,
            $this->iriConverterMock,
            $this->managerRegistryMock,
            $this->resourceMetadataFactoryMock
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
        $this->mockAssociationMetadata(['targetEntity' => Child::class, 'mappedBy' => 'parent']);
        $this->mockRelatedResourceMetadata(['filters' => ['attribute_filter_something_something']]);
        $this->mockRelatedFilterDescription(['parent' => ['strategy' => 'exact']]);
        $this->mockGeneratedRoute();

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => ParentEntity::class]);

        // then
        $this->shouldReplaceChildrenWithLink($result);
    }

    public function testNormalizeDoesntReplaceWhenFilterDoesntApplyToMappedProperty() {
        // given
        $resource = new ParentEntity();
        $this->mockDecoratedNormalizer();
        $this->mockAssociationMetadata(['targetEntity' => Child::class, 'mappedBy' => 'parent']);
        $this->mockRelatedResourceMetadata(['filters' => ['attribute_filter_something_something']]);
        $this->mockRelatedFilterDescription(['some_other_property' => ['strategy' => 'exact']]);
        $this->mockGeneratedRoute();

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => ParentEntity::class]);

        // then
        $this->shouldNotReplaceChildren($result);
    }

    public function testNormalizeDoesntReplaceWhenStrategyIsNotExact() {
        // given
        $resource = new ParentEntity();
        $this->mockDecoratedNormalizer();
        $this->mockAssociationMetadata(['targetEntity' => Child::class, 'mappedBy' => 'parent']);
        $this->mockRelatedResourceMetadata(['filters' => ['attribute_filter_something_something']]);
        $this->mockRelatedFilterDescription(['parent' => ['strategy' => 'start']]);
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
        $this->mockAssociationMetadata(['mappedBy' => 'parent']);
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
        $this->mockAssociationMetadata(['targetEntity' => Child::class]);
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

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager->method('getClassMetadata')->willReturn($classMetadata);

        $this->managerRegistryMock->method('getManagerForClass')->willReturn($manager);
    }

    protected function mockRelatedResourceMetadata($metadata) {
        $this->resourceMetadataFactoryMock->method('create')->willReturn(new ResourceMetadata(null, null, null, null, null, $metadata));
    }

    protected function mockRelatedFilterDescription($description) {
        $this->filterInstance = $this->createMock(SearchFilter::class);
        $this->filterInstance->method('getDescription')->willReturn($description);
    }

    protected function shouldReplaceChildrenWithLink($result, $link = '/children?parent=/parents/123') {
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

    protected function mockGeneratedRoute($generated = '/children?parent=/parents/123') {
        $this->routerMock->method('generate')->willReturn($generated);
    }
}

class ParentEntity {
    private string $id = '123';

    private string $hello = 'world';

    /**
     * @ORM\OneToMany(targetEntity=Child::class, mappedBy="parent")
     */
    private Collection $children;

    /**
     * @ORM\OneToOne(targetEntity=Child::class)
     */
    private ?Child $firstBorn;
}

#[ApiFilter(SearchFilter::class, properties: ['parent'])]
class Child {
    /**
     * @ORM\ManyToOne(targetEntity=ParentEntity::class, inversedBy="children")
     */
    private ?ParentEntity $parent;
}
