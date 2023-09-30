<?php

namespace App\Validator;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AssertCollectionIsNotEmptyValidator extends ConstraintValidator {
    public function __construct(
        private RequestStack $requestStack,
        private EntityManagerInterface $em
    ) {}

    public function validate(mixed $value, Constraint $constraint) {
        if (!$constraint instanceof AssertCollectionIsNotEmpty) {
            throw new UnexpectedTypeException($constraint, AssertCollectionIsNotEmpty::class);
        }
        if (!$value instanceof PersistentCollection) {
            throw new UnexpectedTypeException($value, PersistentCollection::class);
        }

        $method = $this->requestStack->getCurrentRequest()->getMethod();

        if ('DELETE' == $method) {
            $collection = $value;
            $object = $collection->getOwner();
            $this->em->lock($object, LockMode::PESSIMISTIC_WRITE);

            $count = $collection->count();

            if ($count <= 1) {
                $this->context->buildViolation($constraint->message)
                    ->setInvalidValue($value)
                    ->setCode(AssertCollectionIsNotEmpty::IS_EMPTY_ERROR)
                    ->addViolation()
                ;

                return;
            }
        }
    }
}
