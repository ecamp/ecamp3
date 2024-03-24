<?php

/** @noinspection PhpInternalEntityUsedInspection */

namespace App\Serializer\Normalizer;

use ApiPlatform\Serializer\AbstractConstraintViolationListNormalizer;
use App\Serializer\Normalizer\Error\TranslationInfoOfConstraintViolation;
use App\Service\TranslateToAllLocalesService;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class TranslationConstraintViolationListNormalizer implements NormalizerInterface {
    public function __construct(
        private readonly AbstractConstraintViolationListNormalizer $hydraNormalizer,
        private readonly AbstractConstraintViolationListNormalizer $problemNormalizer,
        private readonly TranslationInfoOfConstraintViolation $translationInfoOfConstraintViolation,
        private readonly TranslateToAllLocalesService $translateToAllLocalesService
    ) {}

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool {
        return $this->getNormalizerCollection()->exists(fn ($_, $elem) => $elem->supportsNormalization($data, $format, $context));
    }

    public function normalize(mixed $object, ?string $format = null, array $context = []): null|array|\ArrayObject|bool|float|int|string {
        $normalizer = $this->getNormalizerCollection()->filter(fn ($elem) => $elem->supportsNormalization($object, $format, $context))->first();
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
                        array_merge(
                            $violation->getPlural() ? ['%count%' => $violation->getPlural()] : [],
                            $violation->getParameters()
                        )
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

    public function getSupportedTypes(?string $format): array {
        return $this->getNormalizerCollection()
            ->map(function (AbstractConstraintViolationListNormalizer $normalizer) use ($format) {
                return $normalizer->getSupportedTypes($format);
            })
            ->reduce(fn (?array $left, array $right) => array_merge($left ?? [], $right), [])
        ;
    }

    private function getNormalizerCollection(): ArrayCollection {
        return new ArrayCollection([$this->hydraNormalizer, $this->problemNormalizer]);
    }
}
