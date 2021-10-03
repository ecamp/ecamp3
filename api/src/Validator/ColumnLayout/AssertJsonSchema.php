<?php

namespace App\Validator\ColumnLayout;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class AssertJsonSchema extends Constraint {
    public string $message = "Provided JSON doesn't match required schema.";

    public array $schema = [
        'type' => 'array',
        'items' => [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['slot', 'width'],
            'properties' => [
                'slot' => [
                    'type' => 'string',
                    'pattern' => '^[1-9][0-9]*$',
                ],
                'width' => [
                    'type' => 'integer',
                    'minimum' => 3,
                    'maximum' => 12,
                ],
            ],
        ],
    ];

    public function __construct(array $options = null, string $message = null, array $schema = null, array $groups = null, $payload = null) {
        parent::__construct($options ?? [], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->schema = $schema ?? $this->schema;
    }
}
