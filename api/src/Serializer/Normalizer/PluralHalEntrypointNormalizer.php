<?php

namespace App\Serializer\Normalizer;

use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\String\Inflector\EnglishInflector;

/**
 * This class modifies the API entrypoint when retrieved in HAL JSON format (/index.jsonhal), such
 * that the relation names of the _links are in plural rather than the default singular of API platform.
 */
final class PluralHalEntrypointNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface {
    public function __construct(private NormalizerInterface $decorated, private EnglishInflector $inflector, private ResourceMetadataFactoryInterface $resourceMetadataFactory) {
    }

    public function supportsNormalization($data, $format = null): bool {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function normalize($object, $format = null, array $context = []) {
        $entrypoint = $this->decorated->normalize($object, $format, $context);

        foreach ($object->getResourceNameCollection() as $resourceClass) {
            $resourceMetadata = $this->resourceMetadataFactory->create($resourceClass);
            $singularName = lcfirst($resourceMetadata->getShortName());

            if (!isset($entrypoint['_links'][$singularName])) {
                continue;
            }
            // pluralize will never return an empty array
            $pluralName = $this->inflector->pluralize($singularName)[0];

            // Rename the link rels from singular to plural
            $entrypoint['_links'][$pluralName] = $entrypoint['_links'][$singularName];
            unset($entrypoint['_links'][$singularName]);
        }

        return $entrypoint;
    }

    public function hasCacheableSupportsMethod(): bool {
        if (!$this->decorated instanceof CacheableSupportsMethodInterface) {
            return false;
        }

        return $this->decorated->hasCacheableSupportsMethod();
    }
}
