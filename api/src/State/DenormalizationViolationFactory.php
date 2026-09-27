<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\DenormalizationViolationFactoryInterface;
use ApiPlatform\Symfony\Validator\ValidationGroupsGeneratorInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Exception\PartialDenormalizationException;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * api-platform/validator hands operation.validationContext['groups'] straight to
 * DenormalizationViolationFactory::collectConstraints(..., ?array $validationGroups),
 * while api-platform/symfony's Validator resolves that same value from a
 * ValidationGroupsGeneratorInterface service into a GroupSequence. An operation that
 * names its groups by such a class therefore validates correctly and still answers
 * HTTP 500 with a TypeError for every denormalization violation, because TypeError is
 * absent from the exception_to_status map. Resolve the class to the groups it yields
 * for the error path only, leaving the operation's group sequence intact.
 */
class DenormalizationViolationFactory implements DenormalizationViolationFactoryInterface {
    public function __construct(private readonly DenormalizationViolationFactoryInterface $decorated) {}

    public function handle(NotNormalizableValueException|PartialDenormalizationException $exception, Operation $operation): void {
        $groups = ($operation->getValidationContext() ?? [])['groups'] ?? null;

        if (\is_string($groups) && is_a($groups, ValidationGroupsGeneratorInterface::class, true) && null !== $class = $operation->getClass()) {
            $groups = (new $groups())(new $class());
            $operation = $operation->withValidationContext([
                'groups' => $groups instanceof GroupSequence ? $groups->groups : $groups,
            ]);
        }

        try {
            $this->decorated->handle($exception, $operation);
        } catch (ValidationException $validationException) {
            $dateMessages = $this->dateMessages($exception);
            if ([] === $dateMessages) {
                throw $validationException;
            }

            $violations = new ConstraintViolationList();
            foreach ($validationException->getConstraintViolationList() as $violation) {
                $path = $violation->getPropertyPath();
                $pathMessages = $dateMessages[$path] ?? [];
                $message = array_shift($pathMessages);
                $dateMessages[$path] = $pathMessages;
                $violations->add(null === $message ? $violation : $this->withMessage($violation, $message));
            }

            throw new ValidationException($violations);
        }
    }

    private function dateMessages(NotNormalizableValueException|PartialDenormalizationException $exception): array {
        $errors = $exception instanceof NotNormalizableValueException
            ? [$exception]
            : $exception->getNotNormalizableValueErrors();
        $messages = [];

        foreach ($errors as $error) {
            if ($error instanceof NotNormalizableValueException && (str_starts_with($error->getMessage(), 'Parsing datetime string ') || str_starts_with($error->getMessage(), 'Failed to parse time string '))) {
                $messages[$error->getPath()][] = $error->getMessage();
            }
        }

        return $messages;
    }

    private function withMessage(ConstraintViolationInterface $violation, string $message): ConstraintViolation {
        return new ConstraintViolation(
            $message,
            $message,
            $violation->getParameters(),
            $violation->getRoot(),
            $violation->getPropertyPath(),
            $violation->getInvalidValue(),
            $violation->getPlural(),
            $violation->getCode(),
            $violation->getConstraint(),
        );
    }
}
