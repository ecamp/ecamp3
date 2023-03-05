<?php

namespace App\Validator\AllowTransition;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AssertAllowTransitionsValidator extends ConstraintValidator {
    public const TO_VIOLATION_MESSAGE = 'value must be one of {{ to }}, was {{ value }}';
    public const FROM_VIOLATION_MESSAGE = 'This value was previously in an unexpected state,'.
    ' expected one of {{ from }}, but was {{ previousValue }}';

    public function __construct(public RequestStack $requestStack) {
    }

    public function validate($value, Constraint $constraint) {
        if (!$constraint instanceof AssertAllowTransitions) {
            throw new UnexpectedTypeException($constraint, AssertAllowTransitions::class);
        }

        $transitions = $constraint->getTransitions();
        if (!$transitions->forAll(fn ($_, $elem) => self::isValidTransition($elem))) {
            throw new InvalidArgumentException('All transitions must have a from and a to key');
        }

        $allFrom = $transitions->map(fn (array $elem) => $elem['from'])->toArray();
        $allTo = $transitions->map(fn (array $elem) => $elem['to'])->toArray();
        $allTo = array_merge(...array_values($allTo));
        if (!(new ArrayCollection($allTo))->forAll(fn ($_, $to) => in_array($to, $allFrom, true))) {
            throw new InvalidArgumentException('All to must appear in a from again');
        }

        $previousObject = $this->requestStack->getCurrentRequest()->attributes->get('previous_data');
        $previousValue = $previousObject->{$this->context->getPropertyName()};

        if ($value === $previousValue) {
            return;
        }

        foreach ($transitions as $transition) {
            if ($previousValue === $transition['from']) {
                $to = $transition['to'];
                if (!in_array($value, $to, true)) {
                    $this->context->buildViolation(self::TO_VIOLATION_MESSAGE)
                        ->setParameter('{{ to }}', join(', ', $to))
                        ->setParameter('{{ value }}', $value)
                        ->addViolation()
                    ;
                }

                return;
            }
        }

        $this->context->buildViolation(self::FROM_VIOLATION_MESSAGE)
            ->setParameter('{{ from }}', join(',', $allFrom))
            ->setParameter('{{ previousValue }}', $previousValue)
            ->addViolation()
        ;
    }

    private static function isValidTransition($elem): bool {
        return isset($elem['from']) && isset($elem['to']) && is_array($elem['to']);
    }
}
