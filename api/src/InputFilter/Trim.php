<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\InputFilter;

/**
 * Trims whitespace from beginning and end of a string using the PHP trim() function.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Trim extends InputFilter {
    /**
     * {@inheritdoc}
     */
    function applyTo(array $data, string $propertyName): array {
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
