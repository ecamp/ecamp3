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

        // $seen keeps track of all parents that we have visited. This is for a safety
        // bailout mechanism to avoid an infinite loop in case there is flawed data in the DB
        $seen = [];

        while (null !== $parent && !in_array($parent->getId(), $seen)) {
            if ($parent->getId() === $object->getId()) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation()
                ;

                return;
            }

            $seen[] = $parent->getId();
            $parent = $parent->parent;
        }
    }
}
