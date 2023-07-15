<?php

namespace App\Validator\Camp;

use App\Service\CampCouponService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AssertValidCouponKeyValidator extends ConstraintValidator {
    public function __construct(private CampCouponService $couponService) {
    }

    public function validate($value, Constraint $constraint): void {
        if (!$constraint instanceof AssertValidCouponKey) {
            throw new UnexpectedTypeException($constraint, AssertValidCouponKey::class);
        }

        if (!$this->couponService->verifyCoupon($value)) {
            $this->context->buildViolation($constraint->messageInvalid)
                ->addViolation()
            ;
        } elseif (!$this->couponService->isCouponFree($value)) {
            $this->context->buildViolation($constraint->messageAlreadyUsed)
                ->addViolation()
            ;
        }
    }
}
