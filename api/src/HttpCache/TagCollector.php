<?php

declare(strict_types=1);

namespace App\HttpCache;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Serializer\TagCollectorInterface;
use App\Entity\BaseEntity;
use FOS\HttpCacheBundle\Http\SymfonyResponseTagger;

/**
 * Collects cache tags during normalization.
 *
 * @author Urban Suppiger <urban@suppiger.net>
 */
class TagCollector implements TagCollectorInterface {
    public const IRI_RELATION_DELIMITER = '#';

    public function __construct(private SymfonyResponseTagger $responseTagger) {}

    /**
     * Collect cache tags for cache invalidation.
     *
     * @param array<string, mixed>&array{iri?: string, data?: mixed, object?: mixed, property_metadata?: \ApiPlatform\Metadata\ApiProperty, api_attribute?: string, resources?: array<string, string>} $context
     */
    public function collect(array $context = []): void {
        $iri = $context['iri'];
        $object = $context['object'];

        if ($object instanceof BaseEntity) {
            $iri = $object->getId();
        }

        if (isset($context['property_metadata'])) {
            $this->addCacheTagsForRelation($context, $iri, $context['property_metadata']);
        } elseif (\is_array($context['data'])) {
            $this->addCacheTagForResource($context, $iri);
        }
    }

    private function addCacheTagForResource(array $context, ?string $iri): void {
        if (isset($iri)) {
            $this->responseTagger->addTags([$iri]);
        }
    }

    private function addCacheTagsForRelation(array $context, ?string $iri, ApiProperty $propertyMetadata): void {
        if (isset($iri)) {
            if (isset($propertyMetadata->getExtraProperties()['cacheDependencies'])) {
                foreach ($propertyMetadata->getExtraProperties()['cacheDependencies'] as $dependency) {
                    $cacheTag = $iri.PurgeHttpCacheListener::IRI_RELATION_DELIMITER.$dependency;
                    $this->responseTagger->addTags([$cacheTag]);
                }
            } else {
                $cacheTag = $iri.PurgeHttpCacheListener::IRI_RELATION_DELIMITER.$context['api_attribute'];
                $this->responseTagger->addTags([$cacheTag]);
            }
        }
    }
}
