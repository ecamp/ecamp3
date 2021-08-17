<?php

namespace App\Serializer\Normalizer;

use App\Entity\ContentNode;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class ContentNodeStrategyNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface {
    private $decorated;

    public function __construct(NormalizerInterface $decorated) {
        if (!$decorated instanceof DenormalizerInterface) {
            throw new \InvalidArgumentException(sprintf('The decorated normalizer must implement the %s.', DenormalizerInterface::class));
        }

        $this->decorated = $decorated;
    }

    public function supportsNormalization($data, $format = null) {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function normalize($object, $format = null, array $context = []) {
        $data = $this->decorated->normalize($object, $format, $context);

        if ($object instanceof ContentNode) {
            if (is_array($data)) {
                $strategyClass = $object->contentType->strategyClass;
                $data['strategyClass'] = $strategyClass;
                $strategy = new $strategyClass();
                $data['strategyData'] = $strategy->contentNodeExtract($object);
            }
        }

        return $data;
    }

    public function supportsDenormalization($data, $type, $format = null) {
        return $this->decorated->supportsDenormalization($data, $type, $format);
    }

    public function denormalize($data, $class, $format = null, array $context = []) {
        return $this->decorated->denormalize($data, $class, $format, $context);
    }

    public function setSerializer(SerializerInterface $serializer) {
        if ($this->decorated instanceof SerializerAwareInterface) {
            $this->decorated->setSerializer($serializer);
        }
    }
}
