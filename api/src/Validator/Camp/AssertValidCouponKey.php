<?php

namespace App\Validator\Camp;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AssertValidCouponKey extends Constraint {
    public string $messageInvalid = 'Invalid Coupon-Key';
    public string $messageAlreadyUsed = 'Coupon-Key already used';
}
