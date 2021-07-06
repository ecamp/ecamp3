<?php

namespace App\Serializer\Normalizer;

use ApiPlatform\Core\Api\FilterInterface;
use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Api\OperationType;
use ApiPlatform\Core\Api\UrlGeneratorInterface;
use ApiPlatform\Core\Bridge\Doctrine\Common\PropertyHelperTrait;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Symfony\Routing\RouteNameResolverInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Routing\RouterInterface;
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
 */
class RelatedCollectionLinkNormalizer implements NormalizerInterface, SerializerAwareInterface {
    use PropertyHelperTrait;

    private NormalizerInterface $decorated;
    private RouterInterface $router;
    private RouteNameResolverInterface $routeNameResolver;
    private ServiceLocator $filterLocator;
    private IriConverterInterface $iriConverter;
    private ManagerRegistry $managerRegistry;
    private ResourceMetadataFactoryInterface $resourceMetadataFactory;

    public function __construct(NormalizerInterface $decorated, RouteNameResolverInterface $routeNameResolver, ServiceLocator $filterLocator, RouterInterface $router, IriConverterInterface $iriConverter, ManagerRegistry $managerRegistry, ResourceMetadataFactoryInterface $resourceMetadataFactory) {
        $this->decorated = $decorated;
        $this->router = $router;
        $this->routeNameResolver = $routeNameResolver;
        $this->filterLocator = $filterLocator;
        $this->iriConverter = $iriConverter;
        $this->managerRegistry = $managerRegistry;
        $this->resourceMetadataFactory = $resourceMetadataFactory;
    }

    public function supportsNormalization($data, $format = null) {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function normalize($object, $format = null, array $context = []) {
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
        $resourceClass = $context['resource_class'] ?? get_class($object);

        try {
            $relationMetadata = $this->getClassMetadata($resourceClass)->getAssociationMapping($rel);
        } catch (MappingException) {
            throw new UnsupportedRelationException($resourceClass.'#'.$rel.' is not a Doctrine association. Embedding non-Doctrine collections is currently not implemented.');
        }

        if (!isset($relationMetadata['targetEntity']) || '' === $relationMetadata['targetEntity'] || !isset($relationMetadata['mappedBy']) || '' === $relationMetadata['mappedBy']) {
            throw new UnsupportedRelationException('The '.$resourceClass.'#'.$rel.' relation does not have both a targetEntity and a mappedBy property');
        }

        $relatedResourceClass = $relationMetadata['targetEntity'];
        /** @var string $relatedFilterName */
        $relatedFilterName = $relationMetadata['mappedBy'];

        if (!$this->exactSearchFilterExists($relatedResourceClass, $relatedFilterName)) {
            throw new UnsupportedRelationException('The resource '.$relatedResourceClass.' does not have a search filter for the relation '.$relatedFilterName.'.');
        }

        return $this->router->generate($this->routeNameResolver->getRouteName($relatedResourceClass, OperationType::COLLECTION), [$relatedFilterName => $this->iriConverter->getIriFromItem($object)], UrlGeneratorInterface::ABS_PATH);
    }

    protected function getManagerRegistry(): ManagerRegistry {
        return $this->managerRegistry;
    }

    /**
     * @throws \ApiPlatform\Core\Exception\ResourceClassNotFoundException
     */
    private function exactSearchFilterExists(string $resourceClass, mixed $propertyName): bool {
        $resourceMetadata = $this->resourceMetadataFactory->create($resourceClass);
        $filterIds = $resourceMetadata->getAttribute('filters') ?? [];

        return count(array_filter($filterIds, function ($filter) use ($resourceClass, $propertyName) {
            /** @var FilterInterface $filter */
            $filter = $this->filterLocator->get($filter);
            if (!($filter instanceof SearchFilter)) {
                return false;
            }
            $filterDescription = $filter->getDescription($resourceClass);

            return array_key_exists($propertyName, $filterDescription)
                && isset($filterDescription[$propertyName]['strategy'])
                && 'exact' === $filterDescription[$propertyName]['strategy'];
        }));
    }
}
