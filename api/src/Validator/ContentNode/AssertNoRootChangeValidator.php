<?php

namespace App\Validator\ContentNode;

use App\Entity\ContentNode;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class AssertNoRootChangeValidator extends ConstraintValidator {
    public function __construct(public RequestStack $requestStack) {
    }

    public function validate($value, Constraint $constraint) {
        if (!$constraint instanceof AssertNoRootChange) {
            throw new UnexpectedTypeException($constraint, AssertNoRootChange::class);
        }

        $object = $this->context->getObject();
        if (!$object instanceof ContentNode) {
            throw new UnexpectedValueException($object, ContentNode::class);
        }

        $previousObject = $this->requestStack->getCurrentRequest()?->attributes?->get('previous_data');
        $previousValue = $previousObject?->{$this->context->getPropertyName()};

        // root nodes have parent:null (before and after)
        if ($previousObject instanceof ContentNode && null === $value && null === $previousValue) {
            return;
        }

        if (!$value instanceof ContentNode) {
            throw new UnexpectedValueException($value, ContentNode::class);
        }

        if ($value->getRoot()->getId() !== $object->getRoot()->getId() || null === $value->getRoot()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}
