<?php

namespace App\State;

use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ApiResource\Error;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use App\DTO\ValidationError;
use App\Serializer\Normalizer\Error\TranslationInfoOfConstraintViolation;
use App\Service\TranslateToAllLocalesService;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;

/**
 * @psalm-suppress MissingTemplateParam
 */
class ValidationErrorProvider implements ProviderInterface {
    public function __construct(
        private readonly ProviderInterface $decorated,
        private readonly TranslationInfoOfConstraintViolation $translationInfoOfConstraintViolation,
        private readonly TranslateToAllLocalesService $translateToAllLocalesService,
        private readonly MetadataAwareNameConverter $nameConverter
    ) {}

    /**
     * @psalm-suppress InvalidReturnStatement
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|object|null {
        $request = $context['request'];
        $exception = $request?->attributes->get('exception');
        if (!($request ?? null) || !$operation instanceof HttpOperation || null === $exception) {
            throw new \RuntimeException('Not an HTTP request');
        }

        $status = $operation->getStatus() ?? 500;
        $error = Error::createFromException($exception, $status);
        if (!$exception instanceof ValidationException) {
            return $this->decorated->provide($operation, $uriVariables, $context);
        }

        /**
         * @var ValidationException $exception
         */
        $violationInfos = [];
        foreach ($exception->getConstraintViolationList() as $violation) {
            $violationInfo = $this->translationInfoOfConstraintViolation->extract($violation);
            $translations = $this->translateToAllLocalesService->translate(
                $violation->getMessageTemplate(),
                array_merge(
                    $violation->getPlural() ? ['%count%' => $violation->getPlural()] : [],
                    $violation->getParameters()
                )
            );

            $root = $violation->getRoot();
            $propertyPath = $this->nameConverter->normalize(
                $violation->getPropertyPath(),
                // Violations for denormalization failures (such as an invalid backed enum value) carry
                // no root object, so the property path cannot be converted in the context of a class.
                is_object($root) ? $root::class : null,
                'jsonproblem'
            );

            $violationInfos[] = [
                'code' => $violation->getCode(),
                'propertyPath' => $propertyPath,
                'message' => $violation->getMessage(),
                'i18n' => [
                    ...(array) $violationInfo,
                    'translations' => $translations,
                ],
            ];
        }

        return new ValidationError(
            title: $error->getTitle(),
            status: $status,
            detail: $error->getDetail(),
            instance: $error->getInstance(),
            violations: $violationInfos,
            type: $error->getType()
        );
    }
}
