<?php

namespace App\Validator\Period;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AssertGreaterThanOrEqualToLastScheduleEntryEnd extends Constraint {
    public string $message = 'Due to existing schedule entries, end-date can not be earlier then {{ endDate }}';
}
