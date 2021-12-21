<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class AssertHasAtLeastOneManager extends Constraint {
    public string $message = 'Camp must have at least one manager.';

    public function __construct(array $options = null, array $groups = null, $payload = null) {
        parent::__construct($options ?? [], $groups, $payload);
    }
}
