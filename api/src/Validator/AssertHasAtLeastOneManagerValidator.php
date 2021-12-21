<?php

namespace App\Validator;

use App\Entity\Camp;
use App\Entity\CampCollaboration;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class AssertHasAtLeastOneManagerValidator extends ConstraintValidator {
    public function __construct() {
    }

    public function validate($value, Constraint $constraint) {
        if (!$constraint instanceof AssertHasAtLeastOneManager) {
            throw new UnexpectedTypeException($constraint, AssertHasAtLeastOneManager::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof Camp) {
            throw new UnexpectedValueException($value, Camp::class);
        }

        $hasManager = $value->collaborations->exists(function (int $index, CampCollaboration $campCollaboration) {
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
