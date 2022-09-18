<?php

namespace App\Validator\ContentNode;

use App\Entity\ContentNode;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class AssertSlotSupportedByParentValidator extends ConstraintValidator {
    public function __construct() {
    }

    public function validate($value, Constraint $constraint) {
        if (!$constraint instanceof AssertSlotSupportedByParent) {
            throw new UnexpectedTypeException($constraint, AssertSlotSupportedByParent::class);
        }

        $object = $this->context->getObject();
        if (!$object instanceof ContentNode) {
            throw new UnexpectedValueException($object, ContentNode::class);
        }

        $parent = $object->parent;
        if (null === $parent && null === $value) {
            return;
        }
        if (null === $parent) {
            $this->context->buildViolation($constraint->noParentMessage)->addViolation();

            return;
        }
        $supportedSlotNames = $parent->getSupportedSlotNames();
        if (0 === sizeof($supportedSlotNames)) {
            $this->context->buildViolation($constraint->parentDoesNotSupportChildrenMessage)->addViolation();

            return;
        }
        if (!in_array($value, $supportedSlotNames, true)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ supportedSlotNames }}', join(',', $supportedSlotNames))
                ->setParameter('{{ value }}', $value ?? 'null')
                ->addViolation()
            ;
        }
    }
}
