<?php

namespace App\Serializer\Denormalizer;

use App\InputFilter\InputFilter;
use App\InputFilter\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

/**
 * This class is responsible for reading and calling any InputFilter Attributes on API entities.
 * InputFilters can be used to post-process the payload data submitted in write requests.
 */
class InputFilterDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface {
    use DenormalizerAwareTrait;

    private const ALREADY_CALLED = 'INPUT_FILTER_DENORMALIZER_ALREADY_CALLED';

    /**
     * {@inheritDoc}
     */
    public function denormalize($data, $type, $format = null, array $context = []) {
        $data = $this->filterInputs($data, $type);

        $context[self::ALREADY_CALLED] = true;
        return $this->denormalizer->denormalize($data, $type, $format, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function supportsDenormalization($data, $type, $format = null, array $context = []) {
        // Make sure we don't run this denormalizer twice.
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return is_array($data);
    }

    /**
     * Iterates over all properties of the entity class and applies any filter attributes.
     *
     * Implementation based on Symfony/Component/Validator/Mapping/Loader/AnnotationLoader.php
     *
     * @param $data
     * @param array $context
     * @return array
     */
    protected function filterInputs($data, string $className): array {
        if (!is_array($data)) {
            throw new UnexpectedValueException($data);
        }

        $reflClass = $this->getReflectionClass($className);
        $inputFilters = [];
        foreach ($reflClass->getProperties() as $property) {
            if ($property->getDeclaringClass()->name === $className) {
                foreach ($this->getInputFilterAttributes($property) as $inputFilter) {
                    $inputFilters[] = [$property->name, $inputFilter];
                }
            }
        }

        usort($inputFilters, function ($a, $b) {
            // Comparing B to A ensures that priorities are sorted descendingly,
            // as opposed to comparing A to B
            return $b[1]->getPriority() <=> $a[1]->getPriority();
        });

        foreach ($inputFilters as $tuple) {
            $inputFilter = $tuple[1];
            if ($inputFilter instanceof InputFilter) {
                $data = $inputFilter->applyTo($data, $tuple[0]);
            }
        }

        return $data;
    }

    protected function getInputFilterAttributes(object $reflection): \Generator {
        foreach ($reflection->getAttributes(InputFilter::class, \ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            yield $attribute->newInstance();
        }
    }

    protected function getReflectionClass($className) {
        return new \ReflectionClass($className);
    }
}
