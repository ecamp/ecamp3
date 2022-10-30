<?php

namespace App\InputFilter;

/**
 * Removes unsafe character from text.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class CleanText extends FilterAttribute {
    public function __construct(array $options = [], int $priority = 10) {
        parent::__construct($options, $priority);
    }
}
