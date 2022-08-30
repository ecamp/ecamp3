<?php

/** @noinspection PhpInternalEntityUsedInspection */

namespace App\Serializer\Normalizer;

use ApiPlatform\Core\Serializer\AbstractConstraintViolationListNormalizer;
use App\Serializer\Normalizer\Error\TranslationInfoOfConstraintViolation;
use App\Service\TranslateToAllLocalesService;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class TranslationConstraintViolationListNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface {
    public function __construct(
        private readonly AbstractConstraintViolationListNormalizer $decorated,
        private readonly TranslationInfoOfConstraintViolation $translationInfoOfConstraintViolation,
        private readonly TranslateToAllLocalesService $translateToAllLocalesService
    ) {
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function normalize(mixed $object, string $format = null, array $context = []): float|int|bool|\ArrayObject|array|string|null {
        $result = $this->decorated->normalize($object, $format, $context);

        /** @var ConstraintViolationList $object */
        foreach ($object as $violation) {
            foreach ($result['violations'] as &$resultItem) {
                $code = $resultItem['code'];
                $propertyPath = $resultItem['propertyPath'];
                $message = $resultItem['message'];

                if ($violation->getPropertyPath() === $propertyPath
                    && $violation->getCode() === $code
                    && $violation->getMessage() == $message) {
                    $translationInfo = [];
                    if ($violation instanceof ConstraintViolation) {
                        $translationInfo = $this->translationInfoOfConstraintViolation->extract($violation);
                    }

                    $translations = $this->translateToAllLocalesService->translate(
                        $violation->getMessageTemplate(),
                        $violation->getParameters()
                    );

                    $resultItem = [
                        'i18n' => [
                            ...(array) $translationInfo,
                            'translations' => $translations,
                        ],
                        ...$resultItem,
                    ];

                    break;
                }
            }
        }

        return $result;
    }

    public function hasCacheableSupportsMethod(): bool {
        return $this->decorated->hasCacheableSupportsMethod();
    }
}
