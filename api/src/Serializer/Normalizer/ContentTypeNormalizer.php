<?php

namespace App\Serializer\Normalizer;

use ApiPlatform\Core\Api\OperationType;
use ApiPlatform\Core\Api\UrlGeneratorInterface;
use ApiPlatform\Core\Bridge\Symfony\Routing\RouteNameResolverInterface;
use App\Entity\ContentType;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * ...
 */
class ContentTypeNormalizer implements NormalizerInterface, SerializerAwareInterface {
    public function __construct(
        private NormalizerInterface $decorated,
        private RouteNameResolverInterface $routeNameResolver,
        private RouterInterface $router,
    ) {
    }

    public function supportsNormalization($data, $format = null) {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function normalize($object, $format = null, array $context = []) {
        $data = $this->decorated->normalize($object, $format, $context);

        if ($object instanceof ContentType && isset($data['entityClass'])) {
            $data['entityPath'] = $this->router->generate($this->routeNameResolver->getRouteName($data['entityClass'], OperationType::COLLECTION), [], UrlGeneratorInterface::ABS_PATH);
        }

        return $data;
    }

    public function setSerializer(SerializerInterface $serializer) {
        if ($this->decorated instanceof SerializerAwareInterface) {
            $this->decorated->setSerializer($serializer);
        }
    }
}
