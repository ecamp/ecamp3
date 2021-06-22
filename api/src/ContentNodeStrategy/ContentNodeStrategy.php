<?php

namespace App\ContentNodeStrategy;

use App\Entity\ContentNode;

abstract class ContentNodeStrategy {
    public function contentNodeCreated(ContentNode $contentNode, ?ContentNode $prototype = null): void {
    }

    abstract public function getContentTypeEntities(): array;
}
