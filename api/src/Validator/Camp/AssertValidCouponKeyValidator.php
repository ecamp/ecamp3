<?php

namespace App\Validator\Camp;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AssertValidCouponKeyValidator extends ConstraintValidator {
    private $secret = 'test';

    public function __construct() {
    }

    public function validate($value, Constraint $constraint) {
        if (!$constraint instanceof AssertValidCouponKey) {
            throw new UnexpectedTypeException($constraint, AssertValidCouponKey::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $hash = base64_decode($value);

        if (!password_verify($this->secret, $hash)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}
