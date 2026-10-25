<?php

namespace App\Validator\Comment;

use App\Entity\Comment;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * A reply comment is about whatever its thread is about.
 */
class AssertSameAnchorAsParentValidator extends ConstraintValidator {
    public function validate($value, Constraint $constraint): void {
        if (!$constraint instanceof AssertSameAnchorAsParent) {
            throw new UnexpectedTypeException($constraint, AssertSameAnchorAsParent::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof Comment) {
            throw new UnexpectedValueException($value, Comment::class);
        }

        $object = $this->context->getObject();
        if (!$object instanceof Comment) {
            throw new UnexpectedValueException($object, Comment::class);
        }

        foreach (Comment::ANCHOR_FIELDS as $field) {
            $own = $object->{$field};
            if (null !== $own && $own->getId() !== $value->{$field}?->getId()) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ field }}', $field)
                    ->addViolation()
                ;
            }
        }
    }
}
