<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AssertEitherIsNullValidator extends ConstraintValidator {
    public function validate($value, Constraint $constraint) {
        if (!$constraint instanceof AssertEitherIsNull) {
            throw new UnexpectedTypeException($constraint, AssertEitherIsNull::class);
        }

        $other = $constraint->other;
        $object = $this->context->getObject();

        if (!property_exists($object, $other)) {
            throw new InvalidArgumentException(sprintf('The "other" option must be the name of another property ("%s" given).', $other));
        }

        $otherValue = $object->{$other};

        $valueIsNull = (null === $value);
        $otherIsNull = (null === $otherValue);

        if ($valueIsNull && $otherIsNull) {
            $this->context->buildViolation($constraint->messageBothNull)
                ->setParameter('{{ other }}', $other)
                ->addViolation()
            ;
        }

        if (!$valueIsNull && !$otherIsNull) {
            $this->context->buildViolation($constraint->messageNoneNull)
                ->setParameter('{{ other }}', $other)
                ->addViolation()
            ;
        }
    }
}
