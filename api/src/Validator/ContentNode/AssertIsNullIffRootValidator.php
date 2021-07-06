<?php

namespace App\Validator\ContentNode;

use App\Entity\ContentNode;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AssertIsNullIffRootValidator extends ConstraintValidator {
    public function validate($value, Constraint $constraint) {
        if (!$constraint instanceof AssertIsNullIffRoot) {
            throw new UnexpectedTypeException($constraint, AssertIsNullIffRoot::class);
        }

        $object = $this->context->getObject();

        if (!$object instanceof ContentNode) {
            throw new UnexpectedTypeException($object, ContentNode::class);
        }

        $isRoot = (null !== $object->owner);
        $valueIsNull = (null === $value);

        if ($isRoot !== $valueIsNull) {
            $this->context->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}
