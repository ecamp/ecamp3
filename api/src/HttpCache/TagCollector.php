<?php

declare(strict_types=1);

namespace App\HttpCache;

use Symfony\Component\PropertyInfo\Type;
use App\HttpCache\PurgeHttpCacheListener;
use App\Entity\BaseEntity;
use ApiPlatform\Serializer\TagCollectorInterface;
use ApiPlatform\Metadata\ApiProperty;

/**
 * Collects cache tags during normalization.
 *
 * @author Urban Suppiger <urban@suppiger.net>
 */
class TagCollector implements TagCollectorInterface
{
    public const IRI_RELATION_DELIMITER = '#';

    public function collect(mixed $object = null, string $format = null, array $context = [], string $iri = null, mixed $data = null, string $attribute = null, ApiProperty $propertyMetadata = null, Type $type = null): void
    {
        if($attribute){
            $this->addCacheTagsForRelation($context, $iri, $propertyMetadata);
        }
        elseif(is_array($data)){
            $this->addCacheTagForResource($context, $iri);
        }
    }

    private function addCacheTagForResource(array $context, ?string $iri): void
    {
        if (isset($context['resources']) && isset($iri)) {
            $context['resources'][$iri] = $iri;
        }
    }

    private function addCacheTagsForRelation(array $context, ?string $iri, ApiProperty $propertyMetadata): void
    {
        if (isset($context['resources']) && isset($iri)) {
            if (isset($propertyMetadata->getExtraProperties()['cacheDependencies'])) {
                foreach ($propertyMetadata->getExtraProperties()['cacheDependencies'] as $dependency) {
                    $cacheTag = $iri.PurgeHttpCacheListener::IRI_RELATION_DELIMITER.$dependency;
                    $context['resources'][$cacheTag] = $cacheTag;
                }
            } else {
                $cacheTag = $iri.PurgeHttpCacheListener::IRI_RELATION_DELIMITER.$context['api_attribute'];
                $context['resources'][$cacheTag] = $cacheTag;
            }
        }
    }
}