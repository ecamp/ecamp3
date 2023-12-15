<?php

namespace App\Validator\ContentNode;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AssertNoRootChange extends Constraint {
    public string $message = 'Must belong to the same root.';
}
