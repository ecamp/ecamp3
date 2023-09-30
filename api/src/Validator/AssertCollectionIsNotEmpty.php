<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AssertCollectionIsNotEmpty extends Constraint {
    public const IS_EMPTY_ERROR = 'IS_EMPTY_ERROR';
    public string $message = 'This collection should not be empty.';

    public function __construct(
        array $options = null,
        string $message = null,
        array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct($options ?? [], $groups, $payload);

        $this->message ??= $message;
    }
}
