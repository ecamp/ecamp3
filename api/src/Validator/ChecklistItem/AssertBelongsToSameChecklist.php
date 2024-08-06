<?php

namespace App\Validator\ChecklistItem;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AssertBelongsToSameChecklist extends Constraint {
    public string $message = 'Must belong to the same checklist.';
}
