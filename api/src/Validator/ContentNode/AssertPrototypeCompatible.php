<?php

namespace App\Validator\ContentNode;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class AssertPrototypeCompatible extends Constraint {
    public string $messageClassMismatch = 'This value must be an instance of {{ expectedClass }} or a subclass, but was {{ actualClass }}.';
    public string $messageContentTypeMismatch = 'This value must have the content type {{ expectedContentType }}, but was {{ actualContentType }}.';

    public function __construct(array $options = null, string $messageClassMismatch = null, string $messageContentTypeMismatch = null, array $groups = null, $payload = null) {
        parent::__construct($options ?? [], $groups, $payload);

        $this->messageClassMismatch = $messageClassMismatch ?? $this->messageClassMismatch;
        $this->messageContentTypeMismatch = $messageContentTypeMismatch ?? $this->messageContentTypeMismatch;
    }
}
