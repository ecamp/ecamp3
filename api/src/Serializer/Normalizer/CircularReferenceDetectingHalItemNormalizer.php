<?php

namespace App\Serializer\Normalizer;

use ApiPlatform\Api\ResourceClassResolverInterface;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Metadata\Property\Factory\PropertyNameCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Serializer\AbstractItemNormalizer;
use ApiPlatform\Symfony\Security\ResourceAccessCheckerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class CircularReferenceDetectingHalItemNormalizer extends AbstractItemNormalizer implements NormalizerInterface, SerializerAwareInterface {
    /**
     * @internal
     */
    protected const HAL_CIRCULAR_REFERENCE_LIMIT_COUNTERS = 'hal_circular_reference_limit_counters';

    public function __construct(private NormalizerInterface $decorated, PropertyNameCollectionFactoryInterface $propertyNameCollectionFactory, PropertyMetadataFactoryInterface $propertyMetadataFactory, IriConverterInterface $iriConverter, ResourceClassResolverInterface $resourceClassResolver, PropertyAccessorInterface $propertyAccessor = null, NameConverterInterface $nameConverter = null, ClassMetadataFactoryInterface $classMetadataFactory = null, array $defaultContext = [], ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory = null, ?ResourceAccessCheckerInterface $resourceAccessChecker = null) {
        $defaultContext[AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER] = function ($object) {
            return ['_links' => ['self' => ['href' => $this->iriConverter->getIriFromResource($object)]]];
        };
        parent::__construct($propertyNameCollectionFactory, $propertyMetadataFactory, $iriConverter, $resourceClassResolver, $propertyAccessor, $nameConverter, $classMetadataFactory, $defaultContext, $resourceMetadataCollectionFactory, $resourceAccessChecker);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null, array $context = []): bool {
        return $this->decorated->supportsNormalization($data, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = []): null|array|\ArrayObject|bool|float|int|string {
        if ($this->isHalCircularReference($object, $context)) {
            return $this->handleHalCircularReference($object, $format, $context);
        }

        return $this->decorated->normalize($object, $format, $context);
    }

    public function setSerializer(SerializerInterface $serializer): void {
        if ($this->decorated instanceof SerializerAwareInterface) {
            $this->decorated->setSerializer($serializer);
        }
    }

    /**
     * Detects if the configured circular reference limit is reached.
     *
     * @return bool
     *
     * @throws CircularReferenceException
     */
    protected function isHalCircularReference(object $object, array &$context) {
        $objectHash = spl_object_hash($object);

        $circularReferenceLimit = $context[AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT] ?? $this->defaultContext[AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT];
        if (isset($context[self::HAL_CIRCULAR_REFERENCE_LIMIT_COUNTERS][$objectHash])) {
            if ($context[self::HAL_CIRCULAR_REFERENCE_LIMIT_COUNTERS][$objectHash] >= $circularReferenceLimit) {
                unset($context[self::HAL_CIRCULAR_REFERENCE_LIMIT_COUNTERS][$objectHash]);

                return true;
            }

            ++$context[self::HAL_CIRCULAR_REFERENCE_LIMIT_COUNTERS][$objectHash];
        } else {
            $context[self::HAL_CIRCULAR_REFERENCE_LIMIT_COUNTERS][$objectHash] = 1;
        }

        return false;
    }

    /**
     * Handles a circular reference.
     *
     * If a circular reference handler is set, it will be called. Otherwise, a
     * {@class CircularReferenceException} will be thrown.
     *
     * @final
     *
     * @throws CircularReferenceException
     */
    protected function handleHalCircularReference(object $object, string $format = null, array $context = []) {
        $circularReferenceHandler = $context[AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER] ?? $this->defaultContext[AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER];
        if ($circularReferenceHandler) {
            return $circularReferenceHandler($object, $format, $context);
        }

        throw new CircularReferenceException(sprintf('A circular reference has been detected when serializing the object of class "%s" (configured limit: %d).', get_debug_type($object), $context[AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT] ?? $this->defaultContext[AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT]));
    }
}
