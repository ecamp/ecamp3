<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AssertLastCollectionItemIsNotDeleted extends Constraint {
    public const IS_EMPTY_ERROR = 'IS_EMPTY_ERROR';
    public string $message = 'Cannot delete the last entry.';

    public function __construct(
        ?array $options = null,
        ?string $message = null,
        ?array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct($options ?? [], $groups, $payload);

        $this->message = $message ?? $this->message;
    }
}
