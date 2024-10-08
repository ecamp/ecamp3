<?php

namespace App\InputFilter;

/**
 * Lowercases a string using the PHP strtolower() function.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Lowercase extends FilterAttribute {
    public function __construct(array $options = [], int $priority = 10) {
        parent::__construct($options, $priority);
    }
}
