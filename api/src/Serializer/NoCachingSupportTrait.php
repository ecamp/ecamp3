<?php

namespace App\Serializer;

trait NoCachingSupportTrait {
    /**
     * We dont want to cache if this type is supported, because it should only be called once.
     *
     * @see DenormalizerInterface::getSupportedTypes
     */
    public function getSupportedTypes(?string $format): array {
        return ['*' => false];
    }
}
