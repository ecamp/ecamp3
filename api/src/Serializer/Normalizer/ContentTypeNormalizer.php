<?php

namespace App\Serializer\Normalizer;

use App\Entity\ContentType;
use App\Metadata\Resource\Factory\UriTemplateFactory;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * ...
 */
class ContentTypeNormalizer implements NormalizerInterface, SerializerAwareInterface {
    public function __construct(
        private NormalizerInterface $decorated,
        private UriTemplateFactory $uriTemplateFactory,
    ) {
    }

    public function supportsNormalization($data, $format = null) {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function normalize($object, $format = null, array $context = []) {
        $data = $this->decorated->normalize($object, $format, $context);

        if ($object instanceof ContentType && isset($data['entityClass'])) {
            [$uriTemplate, $templated] = $this->uriTemplateFactory->createFromResourceClass($data['entityClass']);
            $data['_links']['contentNodes']['href'] = $uriTemplate;
            $data['_links']['contentNodes']['templated'] = $templated;

            unset($data['entityClass']);
        }

        return $data;
    }

    public function setSerializer(SerializerInterface $serializer) {
        if ($this->decorated instanceof SerializerAwareInterface) {
            $this->decorated->setSerializer($serializer);
        }
    }
}
