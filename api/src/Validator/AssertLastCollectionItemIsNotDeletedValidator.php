<?php

namespace App\Validator;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AssertLastCollectionItemIsNotDeletedValidator extends ConstraintValidator {
    public function __construct(
        private RequestStack $requestStack,
        private EntityManagerInterface $em
    ) {}

    public function validate(mixed $value, Constraint $constraint): void {
        if (!$constraint instanceof AssertLastCollectionItemIsNotDeleted) {
            throw new UnexpectedTypeException($constraint, AssertLastCollectionItemIsNotDeleted::class);
        }
        if (!$value instanceof PersistentCollection) {
            throw new UnexpectedTypeException($value, PersistentCollection::class);
        }

        $method = $this->requestStack->getCurrentRequest()->getMethod();

        if ('DELETE' == $method) {
            $collection = $value;
            $object = $this->context->getObject();
            $this->em->lock($object, LockMode::PESSIMISTIC_WRITE);

            $count = $collection->count();

            if ($count <= 1) {
                $this->context->buildViolation($constraint->message)
                    ->setInvalidValue($value)
                    ->setCode(AssertLastCollectionItemIsNotDeleted::IS_EMPTY_ERROR)
                    ->addViolation()
                ;

                return;
            }
        }
    }
}
