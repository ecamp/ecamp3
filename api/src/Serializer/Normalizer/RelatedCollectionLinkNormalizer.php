<?php

namespace App\Serializer\Normalizer;

use ApiPlatform\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Doctrine\Common\PropertyHelperTrait;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\Exception\ResourceClassNotFoundException;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\UrlGeneratorInterface;
use App\Entity\BaseEntity;
use App\Metadata\Resource\Factory\UriTemplateFactory;
use App\Metadata\Resource\OperationHelper;
use App\Util\ClassInfoTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\AssociationMapping;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\InverseSideMapping;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\Mapping\OwningSideMapping;
use Rize\UriTemplate;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * This class modifies the API platform HAL ItemNormalizer, in order to serialize related collections
 * differently than API platform normally does.
 *
 * By default, API platform serializes linked collections as follows:
 * {
 *   ...
 *   _links: {
 *     self: { href: '/parents/11' },
 *     children: [
 *       { href: '/children/1' },
 *       { href: '/children/2' },
 *     }
 *   }
 * }
 *
 * This modified normalizer changes that to:
 * {
 *   ...
 *   _links: {
 *     self: { href: '/parents/11' },
 *     children: { href: '/children?parent=/parents/11' }
 *   }
 * }
 *
 * For this to work, the parent needs to define targetEntity and mappedBy on the relation,
 * and the child needs to define a SearchFilter on the reverse of the relation:
 *
 * #[ApiResource]
 * #[ApiFilter(SearchFilter::class, properties: ['parent'])]
 * class Child {
 *   ...
 * }
 *
 *
 * Alternatively, you can manually set the link by using the #[RelatedCollectionLink()] attribute:
 *
 * public string myParam;
 *
 * #[RelatedCollectionLink('child', ['before' => 'myParam'])]
 * public function getChildren(): array { ... }
 *
 *
 * You can also use getters for filling parameters:
 *
 * public function getSomeGetterParam(): string { return 'something'; }
 *
 * #[RelatedCollectionLink('child', ['before' => 'someGetterParam'])]
 * public function getChildren(): array { ... }
 */
class RelatedCollectionLinkNormalizer implements NormalizerInterface, SerializerAwareInterface {
    use PropertyHelperTrait;
    use ClassInfoTrait;

    /**
     * @var (Operation|string)[]
     */
    private array $exactSearchFilterExistsOperationCache = [];

    public function __construct(
        private NormalizerInterface $decorated,
        private ServiceLocator $filterLocator,
        private NameConverterInterface $nameConverter,
        private UriTemplate $uriTemplate,
        private UriTemplateFactory $uriTemplateFactory,
        private RouterInterface $router,
        private IriConverterInterface $iriConverter,
        private readonly EntityManagerInterface $entityManager,
        private ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory,
        private PropertyAccessorInterface $propertyAccessor,
    ) {}

    #[\Override]
    public function supportsNormalization($data, $format = null, array $context = []): bool {
        return $this->decorated->supportsNormalization($data, $format, $context);
    }

    #[\Override]
    public function normalize($data, $format = null, array $context = []): array|\ArrayObject|bool|float|int|string|null {
        $normalized_data = $this->decorated->normalize($data, $format, $context);

        if (!isset($normalized_data['_links'])) {
            return $normalized_data;
        }

        foreach ($normalized_data['_links'] as $rel => $link) {
            // Only consider array rels (i.e. OneToMany and ManyToMany)
            if (isset($link['href'])) {
                continue;
            }

            // If relation is a public property, this property can be checked to be a non-null value
            $values = get_object_vars($data);
            if (array_key_exists($rel, $values) && null == $values[$rel]) {
                // target-value is NULL
                continue;
            }

            if (!$this->getRelatedCollectionHref($data, $rel, $context, $result)) {
                continue;
            }
            $normalized_data['_links'][$rel] = ['href' => $result];
        }

        return $normalized_data;
    }

    #[\Override]
    public function getSupportedTypes(?string $format): array {
        return $this->decorated->getSupportedTypes($format);
    }

    #[\Override]
    public function setSerializer(SerializerInterface $serializer): void {
        if ($this->decorated instanceof SerializerAwareInterface) {
            $this->decorated->setSerializer($serializer);
        }
    }

    protected function getRelatedCollectionHref($object, $rel, array $context, &$href): bool {
        $resourceClass = $this->getObjectClass($object);

        // @phpstan-ignore instanceof.alwaysTrue
        if ($this->nameConverter instanceof NameConverterInterface) {
            $rel = $this->nameConverter->denormalize($rel, $resourceClass, null, array_merge($context, ['groups' => ['read']]));
        }

        if ($annotation = $this->getRelatedCollectionLinkAnnotation($resourceClass, $rel)) {
            // If there is an explicit annotation, there is no need to inspect the Doctrine metadata
            $params = $this->extractUriParams($object, $annotation->getParams());
            [$uriTemplate] = $this->uriTemplateFactory->createFromResourceClass($annotation->getRelatedEntity());

            $href = $this->uriTemplate->expand($uriTemplate, $params);

            return true;
        }

        try {
            $classMetadata = $this->getClassMetadata($resourceClass);

            // @phpstan-ignore instanceof.alwaysTrue
            if (!$classMetadata instanceof ClassMetadata) {
                throw new \RuntimeException("The class metadata for {$resourceClass} must be an instance of ClassMetadata.");
            }

            $relationMetadata = $classMetadata->getAssociationMapping($rel);
        } catch (MappingException) {
            // $resourceClass # $rel is not a Doctrine association. Embedding non-Doctrine collections is currently not implemented
            return false;
        }

        $relatedResourceClass = $relationMetadata->targetEntity;
        $relatedFilterName = $this->getRelatedProperty($relationMetadata);

        if (empty($relatedResourceClass) || empty($relatedFilterName)) {
            // The $resourceClass # $rel relation does not have both a targetEntity and a mappedBy or inversedBy property
            return false;
        }

        $lookupKey = $relatedResourceClass.':'.$relatedFilterName;
        if (isset($this->exactSearchFilterExistsOperationCache[$lookupKey])) {
            $result = $this->exactSearchFilterExistsOperationCache[$lookupKey];
        } else {
            $result = 'No Operation';
            $resourceMetadataCollection = $this->resourceMetadataCollectionFactory->create($relatedResourceClass);
            $operation = OperationHelper::findOneByType($resourceMetadataCollection, GetCollection::class);

            if (!$operation) {
                // The resource $relatedResourceClass does not implement GetCollection() operation
            } else {
                $filterExists = $this->exactSearchFilterExists($relatedResourceClass, $relatedFilterName);
                if (!$filterExists) {
                    // The resource $relatedResourceClass does not have a search filter for the relation $relatedFilterName
                } else {
                    $result = $operation;
                }
            }
            $this->exactSearchFilterExistsOperationCache[$lookupKey] = $result;
        }

        if ($result instanceof Operation) {
            $href = $this->router->generate($result->getName(), [$relatedFilterName => urlencode($this->iriConverter->getIriFromResource($object))], UrlGeneratorInterface::ABS_PATH);

            return true;
        }

        return false;
    }

    protected function getRelatedCollectionLinkAnnotation(string $className, string $propertyName): ?RelatedCollectionLink {
        try {
            $reflClass = $this->getReflectionClass($className);
            $method = $reflClass->getMethod('get'.ucfirst($propertyName));
            $attributes = $method->getAttributes(RelatedCollectionLink::class);

            return ($attributes[0] ?? null)?->newInstance();
        } catch (\ReflectionException) {
            return null;
        }
    }

    protected function getReflectionClass($className): \ReflectionClass {
        return new \ReflectionClass($className);
    }

    protected function extractUriParams($object, array $params): array {
        $result = [];
        foreach ($params as $param => $value) {
            if ('$this' === $value) {
                $result[$param] = $this->normalizeUriParam($object);
            } else {
                $result[$param] = $this->normalizeUriParam($this->propertyAccessor->getValue($object, $value));
            }
        }

        return $result;
    }

    protected function normalizeUriParam($param): string {
        if ($param instanceof \DateTimeInterface) {
            $param = $param->format(\DateTime::W3C);
        }
        if ($param instanceof BaseEntity) {
            $param = $this->iriConverter->getIriFromResource($param);
        }

        return $param;
    }

    #[\Override]
    protected function getClassMetadata(string $resourceClass): ClassMetadata {
        return $this->entityManager->getClassMetadata($resourceClass);
    }

    /**
     * @throws ResourceClassNotFoundException
     */
    private function exactSearchFilterExists(string $resourceClass, mixed $propertyName): bool {
        $resourceMetadataCollection = $this->resourceMetadataCollectionFactory->create($resourceClass);
        $filterIds = OperationHelper::findOneByType($resourceMetadataCollection, GetCollection::class)?->getFilters() ?? [];

        return 0 < count(array_filter($filterIds, function ($filterId) use ($resourceClass, $propertyName) {
            /** @var SearchFilterInterface $filter */
            $filter = $this->filterLocator->get($filterId);
            if (!$filter instanceof SearchFilter) {
                return false;
            }
            $filterDescription = $filter->getDescription($resourceClass);

            return array_key_exists($propertyName, $filterDescription)
                && isset($filterDescription[$propertyName]['strategy'])
                && 'exact' === $filterDescription[$propertyName]['strategy'];
        }));
    }

    private function getRelatedProperty(AssociationMapping $mapping): ?string {
        if ($mapping instanceof InverseSideMapping) {
            return $mapping->mappedBy ?? null;
        }

        if ($mapping instanceof OwningSideMapping) {
            return $mapping->inversedBy ?? null;
        }

        return null;
    }
}
