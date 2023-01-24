<?php

namespace App\Validator\ContentNode;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class AssertSlotSupportedByParent extends Constraint {
    public const MESSAGE = 'This value should be one of [{{ supportedSlotNames }}], was {{ value }}.';
    public const NO_PARENT_MESSAGE = 'This value must be null because this content_node has no parent.';
    public const PARENT_DOES_NOT_SUPPORT_CHILDREN = 'The parent of this content_node does not support children.';

    public function __construct(
        public readonly string $message = self::MESSAGE,
        public readonly string $noParentMessage = self::NO_PARENT_MESSAGE,
        public readonly string $parentDoesNotSupportChildrenMessage = self::PARENT_DOES_NOT_SUPPORT_CHILDREN,
        array $options = null,
        array $groups = null,
        $payload = null
    ) {
        parent::__construct($options ?? [], $groups, $payload);
    }
}
