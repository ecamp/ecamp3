<?php

namespace App\Serializer\Normalizer;

use ApiPlatform\Api\FilterInterface;
use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Api\UrlGeneratorInterface;
use ApiPlatform\Doctrine\Common\PropertyHelperTrait;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Exception\ResourceClassNotFoundException;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Util\ClassInfoTrait;
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
use Symfony\Component\Serializer\NameConverter\AdvancedNameConverterInterface;
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

    public function __construct(
        private NormalizerInterface $decorated,
        private ServiceLocator $filterLocator,
        private NameConverterInterface $nameConverter,
        private UriTemplate $uriTemplate,
        private UriTemplateFactory $uriTemplateFactory,
        private RouterInterface $router,
        private IriConverterInterface $iriConverter,
        private ManagerRegistry $managerRegistry,
        private ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory,
        private PropertyAccessorInterface $propertyAccessor
    ) {
    }

    public function supportsNormalization($data, $format = null, array $context = []): bool {
        return $this->decorated->supportsNormalization($data, $format, $context);
    }

    public function normalize($object, $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null {
        $data = $this->decorated->normalize($object, $format, $context);

        if (!isset($data['_links'])) {
            return $data;
        }

        foreach ($data['_links'] as $rel => $link) {
            // Only consider array rels (i.e. OneToMany and ManyToMany)
            if (isset($link['href'])) {
                continue;
            }

            try {
                $data['_links'][$rel] = ['href' => $this->getRelatedCollectionHref($object, $rel, $context)];
            } catch (UnsupportedRelationException $e) {
                // The relation is not supported, or there is no matching filter defined on the related entity
                continue;
            }
        }

        return $data;
    }

    public function setSerializer(SerializerInterface $serializer) {
        if ($this->decorated instanceof SerializerAwareInterface) {
            $this->decorated->setSerializer($serializer);
        }
    }

    public function getRelatedCollectionHref($object, $rel, array $context = []): string {
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
