<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

#[\Attribute]
class AssertEitherIsNull extends Constraint {
    public string $messageBothNull = 'Either this value or {{ other }} should not be null.';
    public string $messageNoneNull = 'Either this value or {{ other }} should be null.';
    public string $other;

    public function __construct(array $options = null, string $other = null, string $messageBothNull = null, string $messageNoneNull = null, array $groups = null, $payload = null) {
        parent::__construct($options ?? [], $groups, $payload);

        $this->messageBothNull = $messageBothNull ?? $this->messageBothNull;
        $this->messageNoneNull = $messageNoneNull ?? $this->messageNoneNull;

        if (null === $other) {
            throw new InvalidArgumentException('The "other" option must be the name of another property.');
        }

        $this->other = $other;
    }
}
