<?php

namespace App\Validator\ContentNode;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class AssertContentTypeCompatible extends Constraint {
    public string $message = 'Selected contentType {{ contentTypeName }} is incompatible with entity of type {{ givenEntityClass }} (expected {{ expectedEntityClass }}).';
}
