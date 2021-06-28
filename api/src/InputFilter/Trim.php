<?php

namespace App\InputFilter;

/**
 * Trims whitespace from beginning and end of a string using the PHP trim() function.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Trim extends FilterAttribute {
    public function __construct(array $options = [], int $priority = 10) {
        parent::__construct($options, $priority);
    }
}
