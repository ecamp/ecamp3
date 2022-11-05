<?php

/** @noinspection PhpInternalEntityUsedInspection */

namespace App\Serializer\Normalizer;

use ApiPlatform\Core\Serializer\AbstractConstraintViolationListNormalizer;
use App\Serializer\Normalizer\Error\TranslationInfoOfConstraintViolation;
use App\Service\TranslateToAllLocalesService;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class TranslationConstraintViolationListNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface {
    public function __construct(
        private readonly AbstractConstraintViolationListNormalizer $hydraNormalizer,
        private readonly AbstractConstraintViolationListNormalizer $problemNormalizer,
        private readonly TranslationInfoOfConstraintViolation $translationInfoOfConstraintViolation,
        private readonly TranslateToAllLocalesService $translateToAllLocalesService
    ) {
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool {
        return $this->getNormalizerCollection()->exists(fn ($_, $elem) => $elem->supportsNormalization($data, $format));
    }

    public function normalize(mixed $object, string $format = null, array $context = []): float|int|bool|\ArrayObject|array|string|null {
        $normalizer = $this->getNormalizerCollection()->filter(fn ($elem) => $elem->supportsNormalization($object, $format))->first();
        if (false === $normalizer) {
            throw new \RuntimeException("Did not find a normalizer to normalize response to format {$format}");
        }
        $result = $normalizer->normalize($object, $format, $context);

        /** @var ConstraintViolationList $object */
        foreach ($object as $violation) {
            foreach ($result['violations'] as &$resultItem) {
                $code = $resultItem['code'] ?? null;
                $propertyPath = $resultItem['propertyPath'];
                $message = $resultItem['message'] ?? null;

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
        return $this->getNormalizerCollection()->forAll(fn ($_, $elem) => $elem->hasCacheableSupportsMethod());
    }

    private function getNormalizerCollection(): ArrayCollection {
        return new ArrayCollection([$this->hydraNormalizer, $this->problemNormalizer]);
    }
}
