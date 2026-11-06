<?php

declare(strict_types=1);

namespace App\HttpCache;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Serializer\TagCollectorInterface;
use App\Entity\HasId;
use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

/**
 * Collects cache tags during normalization.
 *
 * @author Urban Suppiger <urban@suppiger.net>
 */
class TagCollector implements TagCollectorInterface {
    public function __construct(
        private readonly ResponseTagger $responseTagger,
        private readonly NameConverterInterface $nameConverter,
    ) {}

    /**
     * Collect cache tags for cache invalidation.
     *
     * @param array<string, mixed>&array{iri?: string, data?: mixed, object?: mixed, property_metadata?: ApiProperty, api_attribute?: string, resources?: array<string, string>, request_uri?: string, root_operation?: Operation} $context
     */
    public function collect(array $context = []): void {
        $iri = $context['iri'] ?? null;
        $object = $context['object'] ?? null;

        if ($object && $object instanceof HasId) {
            $iri = $object->getId();
        }

        if (!$iri) {
            return;
        }

        if (isset($context['property_metadata'])) {
            $this->addCacheTagForRelation($context, $iri);

            return;
        }

        // Don't include "link-only" resources (=non fully embedded resources)
        if ($this->isLinkOnly($context)) {
            return;
        }

        $this->addCacheTagForResource($iri);

        // add resource specific tags
        if ($object instanceof CanGenerateTagsInterface) {
            $this->responseTagger->addTags($object->getCacheTags());
        }
    }

    private function addCacheTagForResource(string $iri): void {
        $this->responseTagger->addTags([$iri]);
    }

    private function addCacheTagForRelation(array $context, string $iri): void {
        $class = isset($context['object']) ? ClassUtils::getClass($context['object']) : null;
        $relation = $this->nameConverter->normalize($context['api_attribute'], $class);
        $cacheTag = CacheTag::forRelation($iri, $relation);
        $this->responseTagger->addTags([$cacheTag]);
    }

    /**
     * Returns true, if a resource was normalized into a link only
     * Returns false, if a resource was normalized into a fully embedded resource.
     */
    private function isLinkOnly(array $context): bool {
        $format = $context['format'] ?? null;
        $data = $context['data'] ?? null;

        // resource was normalized into JSONAPI link format
        if ('jsonapi' === $format && isset($data['data']) && \is_array($data['data']) && array_keys($data['data']) === ['type', 'id']) {
            return true;
        }

        // resource was normalized into a string IRI only
        if (\in_array($format, ['jsonld', 'jsonhal'], true) && \is_string($data)) {
            return true;
        }

        return false;
    }
}
