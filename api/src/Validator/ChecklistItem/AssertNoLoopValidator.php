<?php

namespace App\Validator\ChecklistItem;

use App\Entity\ChecklistItem;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class AssertNoLoopValidator extends ConstraintValidator {
    public function validate($value, Constraint $constraint): void {
        if (!$constraint instanceof AssertNoLoop) {
            throw new UnexpectedTypeException($constraint, AssertNoLoop::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $object = $this->context->getObject();
        if (!$object instanceof ChecklistItem) {
            throw new UnexpectedValueException($object, ChecklistItem::class);
        }

        /** @var ChecklistItem $parent */
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
