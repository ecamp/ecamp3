<?php

namespace App\Validator\ContentNode;

use App\Entity\ContentNode;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AssertNoLoopValidator extends ConstraintValidator {
    public function validate($value, Constraint $constraint) {
        if (!$constraint instanceof AssertNoLoop) {
            throw new UnexpectedTypeException($constraint, AssertNoLoop::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $object = $this->context->getObject();

        /** @var ContentNode $parent */
        $parent = $value;

        while (null !== $parent) {
            if ($parent->getId() === $object->getId()) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation()
                ;

                return;
            }

            if ($parent->getId() === $parent->parent?->getId()) {
                // Safety bailout to avoid an infinite loop in case there is flawed data in the DB
                return;
            }
            $parent = $parent->parent;
        }
    }
}
