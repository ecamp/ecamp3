<?php

declare(strict_types=1);

namespace App\Validator\Comment;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AssertSameAnchorAsParent extends Constraint {
    public function __construct(
        public readonly string $message = 'Must be about the same {{ field }} as the comment it replies to.',
        ?array $groups = null,
        $payload = null
    ) {
        parent::__construct(null, $groups, $payload);
    }
}
