<?php

namespace eCamp\Core\ContentType;

use eCamp\Core\Entity\ContentNode;
use eCamp\Lib\Service\EntityValidationException;

interface ContentTypeStrategyInterface {
    public function contentNodeExtract(ContentNode $contentNode): array;

    public function contentNodeCreated(ContentNode $contentNode): void;

    /**
     * @param ContentNode $contentNode
     * @throws EntityValidationException
     */
    public function validateContentNode(ContentNode $contentNode): void;
}
