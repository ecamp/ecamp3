<?php

namespace App\Validator\ContentNode;

use ApiPlatform\Core\Util\ClassInfoTrait;
use App\Entity\ContentNode;
use App\Entity\ContentType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class AssertContentTypeCompatibleValidator extends ConstraintValidator {
    use ClassInfoTrait;

    public function __construct() {
    }

    public function validate($value, Constraint $constraint) {
        if (!$constraint instanceof AssertContentTypeCompatible) {
            throw new UnexpectedTypeException($constraint, AssertContentTypeCompatible::class);
        }

        $object = $this->context->getObject();
        if (!$object instanceof ContentNode) {
            throw new UnexpectedValueException($object, ContentNode::class);
        }

        if (!$value instanceof ContentType) {
            throw new UnexpectedValueException($value, ContentType::class);
        }

        if ($value->entityClass !== $this->getObjectClass($object)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ contentTypeName }}', $value->name)
                ->setParameter('{{ givenEntityClass }}', $this->getObjectClass($object))
                ->setParameter('{{ expectedEntityClass }}', $value->entityClass)
                ->addViolation()
            ;
        }
    }
}
