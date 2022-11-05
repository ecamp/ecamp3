<?php

namespace App\Validator\AllowTransition;

use Attribute;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraint;

/**
 * Validation attribute to restrict the transitions of a status properties.
 * Due to the limitations of PHP8 attributes, only constant values can be used.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class AssertAllowTransitions extends Constraint {
    public function __construct(
        public array $transitions,
        array $options = null,
        array $groups = null,
        $payload = null
    ) {
        parent::__construct($options ?? [], $groups, $payload);
    }

    public function getTransitions(): Collection {
        return new ArrayCollection($this->transitions);
    }
}
