<?php

namespace App\Validator\Period;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class AssertNotOverlappingWithOtherPeriods extends Constraint {
    public const DEFAULT_MESSAGE = 'Periods must not overlap.';
    public string $message = self::DEFAULT_MESSAGE;
}
