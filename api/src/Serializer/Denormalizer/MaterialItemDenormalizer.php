<?php

namespace App\Serializer\Denormalizer;

use App\Entity\MaterialItem;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

/**
 * This class is responsible for denormalizing (writing) MaterialItem entities during POST (create) operation
 * Purpose: adds specific query parameters (period, materialNode) to the POST payload.
 */
class MaterialItemDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface {
    use DenormalizerAwareTrait;

    private const ALREADY_CALLED = 'MATERIAL_ITEM_DENORMALIZER_ALREADY_CALLED';

    public function __construct() {
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $type, $format = null, array $context = []) {
        if ('post' === ($context['collection_operation_name'] ?? null)) {
            // copy query parameters to POST payload
            // this allows e.g. posting on /material_items?materialNode=/content_node/material_node/123 without explicitly providing materialNode in POST payload
            $data['period'] ??= $context['request']?->query->get('period');
            $data['materialNode'] ??= $context['request']?->query->get('materialNode');
        }

        $context[self::ALREADY_CALLED] = true;

        return $this->denormalizer->denormalize($data, $type, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, string $type, $format = null, array $context = []) {
        // Make sure we don't run this denormalizer twice.
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return MaterialItem::class === ($context['resource_class'] ?? null);
    }
}
