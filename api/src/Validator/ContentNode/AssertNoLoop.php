<?php

namespace App\Validator\ContentNode;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AssertNoLoop extends Constraint {
    public $message = 'Must not form a loop of parent-child relations.';
}
