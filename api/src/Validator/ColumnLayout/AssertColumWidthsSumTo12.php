<?php

namespace App\Validator\ColumnLayout;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class AssertColumWidthsSumTo12 extends Constraint {
    public string $message = 'Expected column widths to sum to 12, but got a sum of {{ sum }}';

    public function __construct(array $options = null, string $message = null, array $groups = null, $payload = null) {
        parent::__construct($options ?? [], $groups, $payload);

        $this->message = $message ?? $this->message;
    }
}
