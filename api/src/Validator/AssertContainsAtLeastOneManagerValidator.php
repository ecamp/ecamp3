<?php

namespace App\Validator;

use App\Entity\CampCollaboration;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class AssertContainsAtLeastOneManagerValidator extends ConstraintValidator {
    public function __construct() {
    }

    public function validate($value, Constraint $constraint) {
        if (!$constraint instanceof AssertContainsAtLeastOneManager) {
            throw new UnexpectedTypeException($constraint, AssertContainsAtLeastOneManager::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof Collection) {
            throw new UnexpectedValueException($value, Collection::class);
        }

        if ($value->exists(fn ($index, $item) => !$item instanceof CampCollaboration)) {
            throw new UnexpectedValueException(
                $value,
                'Expected collection with items of type '.CampCollaboration::class
            );
        }

        $hasManager = $value->exists(function (int $index, CampCollaboration $campCollaboration) {
            $established = CampCollaboration::STATUS_ESTABLISHED === $campCollaboration->status;
            $isManager = CampCollaboration::ROLE_MANAGER === $campCollaboration->role;

            return $established && $isManager;
        });

        if (!$hasManager) {
            $this->context->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}
