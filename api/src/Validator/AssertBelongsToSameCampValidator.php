<?php

namespace App\Validator;

use App\Entity\BelongsToCampInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class AssertBelongsToSameCampValidator extends ConstraintValidator {
    public function __construct(public RequestStack $requestStack) {
    }

    public function validate($value, Constraint $constraint) {
        if (!$constraint instanceof AssertBelongsToSameCamp) {
            throw new UnexpectedTypeException($constraint, AssertBelongsToSameCamp::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $object = $this->context->getObject();
        if (!$object instanceof BelongsToCampInterface) {
            throw new UnexpectedValueException($object, BelongsToCampInterface::class);
        }

        if (!$value instanceof BelongsToCampInterface) {
            throw new UnexpectedValueException($value, BelongsToCampInterface::class);
        }

        if ($constraint->compareToPrevious) {
            // Get the old state of the entity before the changes were applied.
            $object = $this->requestStack->getCurrentRequest()->attributes->get('previous_data');
        }

        if ($value->getCamp()?->getId() !== $object->getCamp()?->getId() || null === $value->getCamp()?->getId()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}
