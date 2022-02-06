<?php

namespace App\Validator\Period;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class AssertValidPeriodEnd extends Constraint {
    public string $message = 'Due to existing activities, end-date can not be earlier then {{ endDate }}';
}
