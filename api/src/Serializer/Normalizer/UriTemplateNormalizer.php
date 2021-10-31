<?php

namespace App\Serializer\Normalizer;

use App\Metadata\Resource\Factory\UriTemplateFactory;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\String\Inflector\EnglishInflector;

/**
 * This class modifies the API entrypoint when retrieved in HAL JSON format (/index.jsonhal) to include URI templates, and additionally
 * makes sure that the relation names of the _links are in plural rather than the default singular of API platform.
 */
class UriTemplateNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface {
    public function __construct(
        private NormalizerInterface $decorated,
        private EnglishInflector $inflector,
        private UriTemplateFactory $uriTemplateFactory,
    ) {
    }

    public function supportsNormalization($data, $format = null) {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function normalize($object, $format = null, array $context = []) {
        $result = $this->decorated->normalize($object, $format, $context);

        foreach ($result['_links'] as $rel => $link) {
            if ('self' === $rel) {
                continue;
            }

            [$uriTemplate, $templated] = $this->uriTemplateFactory->create($rel);
            if (!$uriTemplate) {
                continue;
            }

            $linkObject = $templated ? ['href' => $uriTemplate, 'templated' => true] : ['href' => $uriTemplate];

            // pluralize() will never return an empty array
            $pluralRel = $this->inflector->pluralize($rel)[0];

            // Unset the old entry first, in case the plural matches the singular
            unset($result['_links'][$rel]);
            $result['_links'][$pluralRel] = $linkObject;
        }

        return $result;
    }

    public function hasCacheableSupportsMethod(): bool {
        if (!$this->decorated instanceof CacheableSupportsMethodInterface) {
            return false;
        }

        return $this->decorated->hasCacheableSupportsMethod();
    }
}
