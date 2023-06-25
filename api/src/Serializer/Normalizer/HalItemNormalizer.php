<?php

/*
 * This file is part of the API Platform project.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Api\ResourceClassResolverInterface;
use ApiPlatform\Api\UrlGeneratorInterface;
use ApiPlatform\Doctrine\Common\PropertyHelperTrait;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Metadata\Property\Factory\PropertyNameCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Util\ClassInfoTrait;
use ApiPlatform\Serializer\AbstractItemNormalizer;
use ApiPlatform\Serializer\CacheKeyTrait;
use ApiPlatform\Serializer\ContextTrait;
use ApiPlatform\Symfony\Security\ResourceAccessCheckerInterface;
use App\Entity\BaseEntity;
use App\Metadata\Resource\Factory\UriTemplateFactory;
use App\Metadata\Resource\OperationHelper;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\Persistence\ManagerRegistry;
use Rize\UriTemplate;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Mapping\AttributeMetadataInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\AdvancedNameConverterInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

/**
 * Converts between objects and array including HAL metadata.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class HalItemNormalizer extends AbstractItemNormalizer {
    use CacheKeyTrait;
    use ClassInfoTrait;
    use ContextTrait;
    use PropertyHelperTrait;

    public const FORMAT = 'jsonhal';

    private array $componentsCache = [];
    private array $attributesMetadataCache = [];

    public function __construct(
        protected PropertyNameCollectionFactoryInterface $propertyNameCollectionFactory,
        protected PropertyMetadataFactoryInterface $propertyMetadataFactory,
        protected IriConverterInterface $iriConverter,
        protected ResourceClassResolverInterface $resourceClassResolver,
        PropertyAccessorInterface $propertyAccessor,
        NameConverterInterface $nameConverter,
        ClassMetadataFactoryInterface $classMetadataFactory,
        array $defaultContext,
        ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory,
        protected ?ResourceAccessCheckerInterface $resourceAccessChecker,
        private ServiceLocator $filterLocator,
        private UriTemplate $uriTemplate,
        private UriTemplateFactory $uriTemplateFactory,
        private RouterInterface $router,
        private ManagerRegistry $managerRegistry
    ) {
        parent::__construct($propertyNameCollectionFactory, $propertyMetadataFactory, $iriConverter, $resourceClassResolver, $propertyAccessor, $nameConverter, $classMetadataFactory, $defaultContext, $resourceMetadataCollectionFactory, $resourceAccessChecker);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool {
        return self::FORMAT === $format && parent::supportsNormalization($data, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null {
        $resourceClass = $this->getObjectClass($object);
        if ($this->getOutputClass($context)) {
            return parent::normalize($object, $format, $context);
        }

        if ($this->resourceClassResolver->isResourceClass($resourceClass)) {
            $resourceClass = $this->resourceClassResolver->getResourceClass($object, $context['resource_class'] ?? null);
        }

        $context = $this->initContext($resourceClass, $context);
        $iri = $this->iriConverter->getIriFromResource($object, UrlGeneratorInterface::ABS_PATH, $context['operation'] ?? null, $context);
        $context['iri'] = $iri;
        $context['api_normalize'] = true;

        if (!isset($context['cache_key'])) {
            $context['cache_key'] = $this->getCacheKey($format, $context);
        }

        $data = parent::normalize($object, $format, $context);
        if (!\is_array($data)) {
            return $data;
        }

        $metadata = [
            '_links' => [
                'self' => [
                    'href' => $iri,
                ],
            ],
        ];
        $components = $this->getComponents($object, $format, $context);
        $metadata = $this->populateRelation($metadata, $object, $format, $context, $components, 'links');
        $metadata = $this->populateRelation($metadata, $object, $format, $context, $components, 'embedded');

        return $metadata + $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool {
        // prevent the use of lower priority normalizers (e.g. serializer.normalizer.object) for this format
        return self::FORMAT === $format;
    }

    /**
     * {@inheritdoc}
     *
     * @throws LogicException
     */
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): never {
        throw new LogicException(sprintf('%s is a read-only format.', self::FORMAT));
    }

    /**
     * {@inheritdoc}
     */
    protected function getAttributes($object, $format = null, array $context = []): array {
        return $this->getComponents($object, $format, $context)['states'];
    }

    protected function getRelatedCollectionLinkAnnotation(string $className, string $propertyName): ?RelatedCollectionLink {
        try {
            $reflClass = $this->getReflectionClass($className);
            $method = $reflClass->getMethod('get'.ucfirst($propertyName));
            $attributes = $method->getAttributes(RelatedCollectionLink::class);

            return ($attributes[0] ?? null)?->newInstance();
        } catch (\ReflectionException $e) {
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

    protected function getManagerRegistry(): ManagerRegistry {
        return $this->managerRegistry;
    }

    private function getRelatedCollectionHref($object, $rel, array $context = []): string {
        $resourceClass = $this->getObjectClass($object);

        if ($this->nameConverter instanceof AdvancedNameConverterInterface) {
            $rel = $this->nameConverter->denormalize($rel, $resourceClass, null, array_merge($context, ['groups' => ['read']]));
        }

        if ($annotation = $this->getRelatedCollectionLinkAnnotation($resourceClass, $rel)) {
            // If there is an explicit annotation, there is no need to inspect the Doctrine metadata
            $params = $this->extractUriParams($object, $annotation->getParams());
            [$uriTemplate] = $this->uriTemplateFactory->createFromResourceClass($annotation->getRelatedEntity());

            return $this->uriTemplate->expand($uriTemplate, $params);
        }

        try {
            $classMetadata = $this->getClassMetadata($resourceClass);

            if (!$classMetadata instanceof ClassMetadataInfo) {
                throw new \RuntimeException("The class metadata for {$resourceClass} must be an instance of ClassMetadataInfo.");
            }

            $relationMetadata = $classMetadata->getAssociationMapping($rel);
        } catch (MappingException) {
            throw new UnsupportedRelationException($resourceClass.'#'.$rel.' is not a Doctrine association. Embedding non-Doctrine collections is currently not implemented.');
        }

        $relatedResourceClass = $relationMetadata['targetEntity'];

        $relatedFilterName = $relationMetadata['mappedBy'];
        $relatedFilterName ??= $relationMetadata['inversedBy'];

        if (empty($relatedResourceClass) || empty($relatedFilterName)) {
            throw new UnsupportedRelationException('The '.$resourceClass.'#'.$rel.' relation does not have both a targetEntity and a mappedBy or inversedBy property');
        }

        $resourceMetadataCollection = $this->resourceMetadataCollectionFactory->create($relatedResourceClass);
        $operation = OperationHelper::findOneByType($resourceMetadataCollection, GetCollection::class);

        if (!$operation) {
            throw new UnsupportedRelationException('The resource '.$relatedResourceClass.' does not implement GetCollection() operation.');
        }

        if (!$this->exactSearchFilterExists($relatedResourceClass, $relatedFilterName)) {
            throw new UnsupportedRelationException('The resource '.$relatedResourceClass.' does not have a search filter for the relation '.$relatedFilterName.'.');
        }

        return $this->router->generate($operation->getName(), [$relatedFilterName => urlencode($this->iriConverter->getIriFromResource($object))], UrlGeneratorInterface::ABS_PATH);
    }

    /**
     * Gets HAL components of the resource: states, links and embedded.
     */
    private function getComponents(object $object, ?string $format, array $context): array {
        $cacheKey = $this->getObjectClass($object).'-'.$context['cache_key'];

        if (isset($this->componentsCache[$cacheKey])) {
            return $this->componentsCache[$cacheKey];
        }

        $attributes = parent::getAttributes($object, $format, $context);
        $options = $this->getFactoryOptions($context);

        $components = [
            'states' => [],
            'links' => [],
            'embedded' => [],
        ];

        foreach ($attributes as $attribute) {
            $propertyMetadata = $this->propertyMetadataFactory->create($context['resource_class'], $attribute, $options);

            // TODO: 3.0 support multiple types, default value of types will be [] instead of null
            $type = $propertyMetadata->getBuiltinTypes()[0] ?? null;
            $isOne = $isMany = false;

            if (null !== $type) {
                if ($type->isCollection()) {
                    $valueType = $type->getCollectionValueTypes()[0] ?? null;
                    $isMany = null !== $valueType && ($className = $valueType->getClassName()) && $this->resourceClassResolver->isResourceClass($className);
                } else {
                    $className = $type->getClassName();
                    $isOne = $className && $this->resourceClassResolver->isResourceClass($className);
                }
            }

            if (!$isOne && !$isMany) {
                $components['states'][] = $attribute;

                continue;
            }

            $relation = ['name' => $attribute, 'cardinality' => $isOne ? 'one' : 'many'];
            if ($propertyMetadata->isReadableLink()) {
                $components['embedded'][] = $relation;
            }

            $components['links'][] = $relation;
        }

        if (false !== $context['cache_key']) {
            $this->componentsCache[$cacheKey] = $components;
        }

        return $components;
    }

    /**
     * Populates _links and _embedded keys.
     */
    private function populateRelation(array $data, object $object, ?string $format, array $context, array $components, string $type): array {
        $class = $this->getObjectClass($object);

        $attributesMetadata = \array_key_exists($class, $this->attributesMetadataCache) ?
            $this->attributesMetadataCache[$class] :
            $this->attributesMetadataCache[$class] = $this->classMetadataFactory ? $this->classMetadataFactory->getMetadataFor($class)->getAttributesMetadata() : null;

        $key = '_'.$type;
        foreach ($components[$type] as $relation) {
            if (null !== $attributesMetadata && $this->isMaxDepthReached($attributesMetadata, $class, $relation['name'], $context)) {
                continue;
            }

            $relationName = $relation['name'];
            if ($this->nameConverter) {
                $relationName = $this->nameConverter->normalize($relationName, $class, $format, $context);
            }

            if ('many' === $relation['cardinality'] && 'links' === $type) {
                try {
                    $data[$key][$relationName]['href'] = $this->getRelatedCollectionHref($object, $relationName, $context);

                    continue;
                } catch (UnsupportedRelationException $e) {
                    // The relation is not supported, or there is no matching filter defined on the related entity
                }
            }

            $attributeValue = $this->getAttributeValue($object, $relation['name'], $format, $context);

            if ('one' === $relation['cardinality']) {
                if ('links' === $type) {
                    if (null !== $attributeValue) {
                        $data[$key][$relationName]['href'] = $this->getRelationIri($attributeValue);

                        continue;
                    }
                }

                $data[$key][$relationName] = $attributeValue;

                continue;
            }

            // many
            $data[$key][$relationName] = [];
            foreach ($attributeValue as $rel) {
                if ('links' === $type) {
                    $rel = ['href' => $this->getRelationIri($rel)];
                }

                $data[$key][$relationName][] = $rel;
            }
        }

        return $data;
    }

    /**
     * Gets the IRI of the given relation.
     *
     * @throws UnexpectedValueException
     */
    private function getRelationIri(mixed $rel): string {
        if (!(\is_array($rel) || \is_string($rel))) {
            throw new UnexpectedValueException('Expected relation to be an IRI or array');
        }

        return \is_string($rel) ? $rel : $rel['_links']['self']['href'];
    }

    /**
     * Is the max depth reached for the given attribute?
     *
     * @param AttributeMetadataInterface[] $attributesMetadata
     */
    private function isMaxDepthReached(array $attributesMetadata, string $class, string $attribute, array &$context): bool {
        if (
            !($context[self::ENABLE_MAX_DEPTH] ?? false)
            || !isset($attributesMetadata[$attribute])
            || null === $maxDepth = $attributesMetadata[$attribute]->getMaxDepth()
        ) {
            return false;
        }

        $key = sprintf(self::DEPTH_KEY_PATTERN, $class, $attribute);
        if (!isset($context[$key])) {
            $context[$key] = 1;

            return false;
        }

        if ($context[$key] === $maxDepth) {
            return true;
        }

        ++$context[$key];

        return false;
    }

    /**
     * @throws ResourceClassNotFoundException
     */
    private function exactSearchFilterExists(string $resourceClass, mixed $propertyName): bool {
        $resourceMetadataCollection = $this->resourceMetadataCollectionFactory->create($resourceClass);
        $filterIds = OperationHelper::findOneByType($resourceMetadataCollection, GetCollection::class)?->getFilters() ?? [];

        return 0 < count(array_filter($filterIds, function ($filterId) use ($resourceClass, $propertyName) {
            /** @var FilterInterface $filter */
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
}
