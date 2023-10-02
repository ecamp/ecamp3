<?php

declare(strict_types=1);

namespace App\HttpCache;

use Symfony\Component\PropertyInfo\Type;
use FOS\HttpCacheBundle\Http\SymfonyResponseTagger;
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

    public function __construct(private SymfonyResponseTagger $responseTagger){

    }

    public function collect(mixed $object = null, string $format = null, array $context = [], string $iri = null, mixed $data = null, string $attribute = null, ApiProperty $propertyMetadata = null, Type $type = null): void
    {
        if($object instanceof BaseEntity){
            $iri = $object->getId();
        }
        
        if($attribute){
            $this->addCacheTagsForRelation($context, $iri, $propertyMetadata);
        }
        elseif(is_array($data)){
            $this->addCacheTagForResource($context, $iri);
        }
    }

    private function addCacheTagForResource(array $context, ?string $iri): void
    {
        if (isset($iri)) {
            $this->responseTagger->addTags([$iri]);
        }
    }

    private function addCacheTagsForRelation(array $context, ?string $iri, ApiProperty $propertyMetadata): void
    {
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