<?php

namespace App\Validator\Period;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class AssertLessThanOrEqualToEarliestScheduleEntryStart extends Constraint {
    public string $message = 'Due to existing schedule entries, start-date can not be later then {{ startDate }}';
}
