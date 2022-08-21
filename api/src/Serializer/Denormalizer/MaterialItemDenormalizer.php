<?php

namespace App\Serializer\Denormalizer;

use App\Entity\MaterialItem;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * This class is responsible for denormalizing (writing) MaterialItem entities during POST (create) operation
 * Purpose: adds specific query parameters (period, materialNode) to the POST payload.
 */
class MaterialItemDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface {
    use DenormalizerAwareTrait;

    private const ALREADY_CALLED = 'MATERIAL_ITEM_DENORMALIZER_ALREADY_CALLED';

    public function __construct(public RequestStack $requestStack) {
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $type, $format = null, array $context = []): mixed {
        if ('post' === ($context['collection_operation_name'] ?? null)) {
            // copy query parameters to POST payload
            // this allows e.g. posting on /material_items?materialNode=/content_node/material_node/123 without explicitly providing materialNode in POST payload
            $data['period'] ??= $this->requestStack->getCurrentRequest()->query->get('period');
            $data['materialNode'] ??= $this->requestStack->getCurrentRequest()->query->get('materialNode');
        }

        $context[self::ALREADY_CALLED] = true;

        return $this->denormalizer->denormalize($data, $type, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool {
        // Make sure we don't run this denormalizer twice.
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return MaterialItem::class === ($context['resource_class'] ?? null);
    }
}
