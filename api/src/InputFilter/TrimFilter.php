<?php

namespace App\InputFilter;

class TrimFilter extends InputFilter {
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
            throw new UnexpectedValueException('Cannot convert value to string for trimming.');
        }

        $value = (string) $value;

        $data[$propertyName] = trim($value);

        return $data;
    }
}
