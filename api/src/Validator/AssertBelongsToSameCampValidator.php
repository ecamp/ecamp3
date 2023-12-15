<?php

namespace App\Validator;

use App\Entity\BelongsToCampInterface;
use App\Entity\BelongsToContentNodeTreeInterface;
use App\Util\GetCampFromContentNodeTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class AssertBelongsToSameCampValidator extends ConstraintValidator {
    use GetCampFromContentNodeTrait;

    public function __construct(
        public RequestStack $requestStack,
        private EntityManagerInterface $em
    ) {}

    public function validate($value, Constraint $constraint): void {
        if (!$constraint instanceof AssertBelongsToSameCamp) {
            throw new UnexpectedTypeException($constraint, AssertBelongsToSameCamp::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $object = $this->context->getObject();
        if (!($object instanceof BelongsToCampInterface || $object instanceof BelongsToContentNodeTreeInterface)) {
            throw new UnexpectedValueException($value, BelongsToCampInterface::class.' or '.BelongsToContentNodeTreeInterface::class);
        }

        if (!($value instanceof BelongsToCampInterface || $value instanceof BelongsToContentNodeTreeInterface)) {
            throw new UnexpectedValueException($value, BelongsToCampInterface::class.' or '.BelongsToContentNodeTreeInterface::class);
        }

        if ($constraint->compareToPrevious) {
            // Get the old state of the entity before the changes were applied.
            $object = $this->requestStack->getCurrentRequest()->attributes->get('previous_data');
        }

        $valueCamp = $this->getCampFromInterface($value, $this->em);

        $objectCamp = $this->getCampFromInterface($object, $this->em);

        if ($valueCamp?->getId() !== $objectCamp?->getId() || null === $valueCamp?->getId()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}
