<?php

declare(strict_types=1);

namespace App\Validator\Period;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AssertGreaterThanOrEqualToLastScheduleEntryEnd extends Constraint {
    public function __construct(
        public readonly string $message = 'Due to existing schedule entries, end-date can not be earlier than {{ endDate }}',
        ?array $groups = null,
        $payload = null
    ) {
        parent::__construct(null, $groups, $payload);
    }
}
