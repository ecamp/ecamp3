<?php

namespace App\Validator;

use App\Entity\BaseEntity;
use App\Entity\BelongsToCampInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class AssertBelongsToSameCampValidator extends ConstraintValidator {
    public function __construct(public EntityManagerInterface $entityManager) {
    }

    public function validate($value, Constraint $constraint) {
        if (!$constraint instanceof AssertBelongsToSameCamp) {
            throw new UnexpectedTypeException($constraint, AssertBelongsToSameCamp::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $object = $this->context->getObject();
        if (!$object instanceof BelongsToCampInterface) {
            throw new UnexpectedValueException($object, BelongsToCampInterface::class);
        }

        if (!$value instanceof BelongsToCampInterface) {
            throw new UnexpectedValueException($value, BelongsToCampInterface::class);
        }

        if ($constraint->compareToPrevious) {
            if (!$object instanceof BaseEntity) {
                throw new UnexpectedValueException($object, BaseEntity::class);
            }

            // Get the old state of the entity before the changes were applied.
            $unitOfWork = $this->entityManager->getUnitOfWork();
            $oldData = $unitOfWork->getOriginalEntityData($object);
            $oldData['id'] = 'reset to make Doctrine ignore the cache';
            $object = $unitOfWork->createEntity(get_class($object), $oldData);
        }

        if ($value->getCamp()?->getId() !== $object->getCamp()?->getId() || null === $value->getCamp()?->getId()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation()
            ;
        }

        if ($constraint->compareToPrevious) {
            // Clean up our temporary old copy of the object, to make sure it is not persisted
            $this->entityManager->detach($object);
        }
    }
}
