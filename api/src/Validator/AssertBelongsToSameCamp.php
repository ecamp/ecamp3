<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AssertBelongsToSameCamp extends Constraint {
    public string $message = 'Must belong to the same camp.';

    public function __construct(?array $options = null, ?string $message = null, ?array $groups = null, mixed $payload = null) {
        parent::__construct($options ?? [], $groups, $payload);

        $this->message = $message ?? $this->message;
    }
}
