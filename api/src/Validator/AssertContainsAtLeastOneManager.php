<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AssertContainsAtLeastOneManager extends Constraint {
    public string $message = 'must have at least one manager.';

    public function __construct(?array $options = null, ?array $groups = null, $payload = null) {
        parent::__construct($options ?? [], $groups, $payload);
    }
}
