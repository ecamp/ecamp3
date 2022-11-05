<?php

namespace App\InputFilter;

class CleanHTMLFilter extends InputFilter {
    private \HTMLPurifier $htmlPurifier;

    public function __construct(\HTMLPurifier $htmlPurifier) {
        $this->htmlPurifier = $htmlPurifier;
    }

    /**
     * {@inheritdoc}
     */
    public function applyTo(array $data, string $propertyName): array {
        if (!array_key_exists($propertyName, $data)) {
            return $data;
        }

        $value = $data[$propertyName];

        if (null === $value) {
            return $data;
        }

        if (!is_scalar($value) && !(\is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedValueException('Cannot convert value to string for HTML cleaning.');
        }

        $value = (string) $value;

        $data[$propertyName] = $this->htmlPurifier->purify($value);

        return $data;
    }
}
