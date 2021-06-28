<?php

namespace App\InputFilter;

/**
 * This attribute should be added to any user-writable string property that could be
 * displayed by frontends (and that is not otherwise validated with a strong format-based
 * validator such as Email, Locale or a restrictive Regex).
 *
 * The priority should also be set such that this input filter runs last. By default, it
 * has a priority of -10.
 *
 * This input filter removes HTML tags from the property to avoid XSS attacks.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class CleanHTML extends FilterAttribute {
    public function __construct(array $options = [], int $priority = -10) {
        parent::__construct($options, $priority);
    }
}
