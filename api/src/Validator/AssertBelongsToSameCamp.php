<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AssertBelongsToSameCamp extends Constraint {
    public string $message = 'Must belong to the same camp.';
    public bool $compareToPrevious = false;

    /**
     * AssertBelongsToSameCamp constructor.
     *
     * @param bool $compareToPrevious in case the camp getter considers the annotated property, use this option (only when updating)
     */
    public function __construct(?array $options = null, bool $compareToPrevious = false, ?string $message = null, ?array $groups = null, mixed $payload = null) {
        parent::__construct($options ?? [], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->compareToPrevious = $compareToPrevious;
    }
}
