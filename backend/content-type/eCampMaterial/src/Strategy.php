<?php

namespace eCamp\ContentType\Material;

use Doctrine\ORM\ORMException;
use eCamp\Core\ContentType\ContentTypeStrategyBase;
use eCamp\Core\Entity\ContentNode;
use eCamp\Lib\Acl\NoAccessException;

class Strategy extends ContentTypeStrategyBase {
    public function contentNodeExtract(ContentNode $contentNode): array {
        return [];
    }

    /**
     * @throws NoAccessException
     * @throws ORMException
     */
    public function contentNodeCreated(ContentNode $contentNode): void {
    }
}
