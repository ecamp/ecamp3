<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class AssertJsonSchema extends Constraint {
    public string $message = "Provided JSON doesn't match required schema ({{ schemaError }}).";

    public array $schema = [
        'type' => 'object',
    ];

    public function __construct(array $options = null, string $message = null, array $schema = null, array $groups = null, $payload = null) {
        parent::__construct($options ?? [], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->schema = $schema ?? $this->schema;
    }
}
