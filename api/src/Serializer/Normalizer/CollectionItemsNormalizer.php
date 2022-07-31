<?php

namespace App\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * This class modifies the API platform HAL CollectionNormalizer, in order to serialize the contained entries
 * under the relation name `items` instead of `item`.
 */
class CollectionItemsNormalizer implements NormalizerInterface, NormalizerAwareInterface {
    private NormalizerInterface $decorated;

    public function __construct(NormalizerInterface $decorated) {
        $this->decorated = $decorated;
    }

    public function supportsNormalization($data, $format = null, array $context = []): bool {
        return $this->decorated->supportsNormalization($data, $format, $context);
    }

    public function normalize($object, $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null {
        $data = $this->decorated->normalize($object, $format, $context);

        if (isset($data['_embedded'], $data['_embedded']['item'])) {
            $data['_embedded']['items'] = $data['_embedded']['item'];
            unset($data['_embedded']['item']);

            $data['_links']['items'] = $data['_links']['item'];
            unset($data['_links']['item']);
        } elseif (isset($data['totalItems']) && 0 === $data['totalItems']) {
            $data['_embedded']['items'] = [];
        }

        return $data;
    }

    public function setNormalizer(NormalizerInterface $normalizer) {
        if ($this->decorated instanceof NormalizerAwareInterface) {
            $this->decorated->setNormalizer($normalizer);
        }
    }
}
