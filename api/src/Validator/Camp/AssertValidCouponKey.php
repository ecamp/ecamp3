<?php

namespace App\Validator\Camp;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AssertValidCouponKey extends Constraint {
    public string $message = 'Invalid Coupon-Key';
}
