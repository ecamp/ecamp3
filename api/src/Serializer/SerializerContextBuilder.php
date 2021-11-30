<?php

namespace App\Serializer;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Adds some common settings to the normalization_context and denormalization_context, so that
 * we don't have to repeat them everywhere.
 *
 * **NOTE**: The context that is set up here is ignored when building the OpenAPI documentation.
 * This means that we shouldn't put any configuration in here that should be reflected in the
 * API documentation. Settings such as skip_null_values and allow_extra_attributes are okay, but
 * settings such as groups are not, because the groups need to be taken into account when
 * building the payload schemas for the API documentation.
 */
final class SerializerContextBuilder implements SerializerContextBuilderInterface {
    private SerializerContextBuilderInterface $decorated;

    public function __construct(SerializerContextBuilderInterface $decorated) {
        $this->decorated = $decorated;
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);

        if ($normalization) {
            $context['skip_null_values'] = false;
        }

        if (!$normalization) {
            $context['allow_extra_attributes'] = false;
        }

        return $context;
    }
}
