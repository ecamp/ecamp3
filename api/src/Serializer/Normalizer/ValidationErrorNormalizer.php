<?php

namespace App\Serializer\Normalizer;

use App\DTO\ValidationError;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ValidationErrorNormalizer implements NormalizerInterface {
    public const TYPE = 'type';
    public const TITLE = 'title';
    private array $defaultContext = [
        self::TYPE => 'https://tools.ietf.org/html/rfc2616#section-10',
        self::TITLE => 'An error occurred',
    ];

    #[\Override]
    public function normalize(mixed $data, ?string $format = null, array $context = []): array|\ArrayObject|bool|float|int|string|null {
        return [
            'type' => $context[self::TYPE] ?? $this->defaultContext[self::TYPE],
            'title' => $context[self::TITLE] ?? $this->defaultContext[self::TITLE],
            'detail' => $data->getDetail(),
            'violations' => $data->getViolations(),
        ];
    }

    #[\Override]
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool {
        return $data instanceof ValidationError;
    }

    #[\Override]
    public function getSupportedTypes(?string $format): array {
        return [ValidationError::class => true];
    }
}
