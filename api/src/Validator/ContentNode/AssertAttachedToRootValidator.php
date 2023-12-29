<?php

namespace App\Validator\ContentNode;

use App\Entity\ContentNode;
use App\Entity\ContentNode\ResponsiveLayout;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class AssertAttachedToRootValidator extends ConstraintValidator {
    public function validate($value, Constraint $constraint): void {
        if (!$constraint instanceof AssertAttachedToRoot) {
            throw new UnexpectedTypeException($constraint, AssertAttachedToRoot::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        // @var ContentNode $node
        $node = $this->context->getObject();
        if (!$node instanceof ContentNode) {
            throw new UnexpectedValueException($node, ContentNode::class);
        }

        if (!$node instanceof ResponsiveLayout) {
            return;
        }

        if ($node->root !== $value) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
