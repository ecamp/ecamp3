<?php

namespace App\Validator\ContentNode;

use App\Entity\ContentNode;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class AssertPrototypeCompatibleValidator extends ConstraintValidator {
    public function validate($value, Constraint $constraint) {
        if (!$constraint instanceof AssertPrototypeCompatible) {
            throw new UnexpectedTypeException($constraint, AssertPrototypeCompatible::class);
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

        $class = get_class($object);

        if (!is_a($value, $class, false)) {
            $this->context->buildViolation($constraint->messageClassMismatch)
                ->setParameter('{{ expectedClass }}', $class)
                ->setParameter('{{ actualClass }}', get_class($value))
                ->addViolation()
            ;
        }

        $actualContentType = $value->getContentTypeName();
        $expectedContentType = $this->context->getObject()->getContentTypeName();

        if ($expectedContentType !== $actualContentType) {
            $this->context->buildViolation($constraint->messageContentTypeMismatch)
                ->setParameter('{{ expectedContentType }}', $expectedContentType)
                ->setParameter('{{ actualContentType }}', $actualContentType)
                ->addViolation()
            ;
        }
    }
}
