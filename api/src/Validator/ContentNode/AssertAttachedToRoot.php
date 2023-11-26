<?php

namespace App\Validator\ContentNode;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AssertAttachedToRoot extends Constraint {
    public $message = 'Must be attached to the root layout.';
}
