<?php

namespace App\Serializer\Normalizer\Error;

class TranslationInfo {
    public function __construct(public readonly string $key, public readonly array $parameters) {
    }
}
