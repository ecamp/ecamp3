<?php

namespace App\Validator\ContentNode;

use App\Entity\ContentNode;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class AssertBelongsToSameOwnerValidator extends ConstraintValidator {
    public function __construct() {
    }

    public function validate($value, Constraint $constraint) {
        if (!$constraint instanceof AssertBelongsToSameOwner) {
            throw new UnexpectedTypeException($constraint, AssertBelongsToSameOwner::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $object = $this->context->getObject();
        if (!$object instanceof ContentNode) {
            throw new UnexpectedValueException($object, ContentNode::class);
        }

        if (!$value instanceof ContentNode) {
            throw new UnexpectedValueException($value, ContentNode::class);
        }

        if ($value->getRootOwner()?->getId() !== $object->getRootOwner()?->getId() || null === $value->getRootOwner()?->getId()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}
