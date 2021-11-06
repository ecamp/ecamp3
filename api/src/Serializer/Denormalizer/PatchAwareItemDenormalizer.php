<?php

declare(strict_types=1);

namespace App\Serializer\Denormalizer;

use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Needed to allow patching embedded relations
 * Copied from here: https://github.com/api-platform/core/issues/4293#issuecomment-831252083
 * Not 100% compliant with JSON Merge Patch specification.
 */
final class PatchAwareItemDenormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface {
    private const OPERATION_NAME = 'patch';

    public function __construct(
        private NormalizerInterface $decorated,
    ) {
        if (!$decorated instanceof DenormalizerInterface) {
            throw new \InvalidArgumentException(sprintf('The decorated normalizer must implement the %s.', DenormalizerInterface::class));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, string $format = null) {
        return $this->decorated->supportsNormalization($data, $format);
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, string $format = null, array $context = []) {
        return $this->decorated->normalize($object, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, string $type, string $format = null) {
        return $this->decorated->supportsDenormalization($data, $type, $format);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, string $type, string $format = null, array $context = []) {
        if (isset($data['id']) && self::OPERATION_NAME === $context['item_operation_name']) {
            // Ensure the the object to populate is loaded by the JSON ItemNormalizer
            unset($context[AbstractNormalizer::OBJECT_TO_POPULATE]);
        }

        $result = $this->decorated->denormalize($data, $type, $format, $context);

        if (false) {
            return '';
        }

        return $result;
    }

    public function setSerializer(SerializerInterface $serializer) {
        if ($this->decorated instanceof SerializerAwareInterface) {
            $this->decorated->setSerializer($serializer);
        }
    }
}
