<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AssertBelongsToSameCamp extends Constraint {
    public function __construct(
        public readonly string $message = 'Must belong to the same camp.',
        ?array $groups = null,
        $payload = null
    ) {
        parent::__construct(null, $groups, $payload);
    }
}
