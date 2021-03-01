<?php

namespace eCamp\Core\ContentType;

use eCamp\Core\Entity\ContentNode;

interface ContentTypeStrategyInterface {
    public function contentNodeExtract(ContentNode $contentNode): array;

    public function contentNodeCreated(ContentNode $contentNode): void;
}
