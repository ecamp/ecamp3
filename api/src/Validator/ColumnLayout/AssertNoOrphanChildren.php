<?php

namespace App\Validator\ColumnLayout;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AssertNoOrphanChildren extends Constraint {
    public string $message = 'The following slots still have child contents and should be present in the columns: {{ slots }}';

    public function __construct(array $options = null, string $message = null, array $groups = null, $payload = null) {
        parent::__construct($options ?? [], $groups, $payload);

        $this->message = $message ?? $this->message;
    }
}
