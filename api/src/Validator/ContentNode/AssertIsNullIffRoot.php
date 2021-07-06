<?php

namespace App\Validator\ContentNode;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class AssertIsNullIffRoot extends Constraint {
    public $message = 'This value should not be null, except in root content nodes.';
}
