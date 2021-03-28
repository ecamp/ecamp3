<?php

namespace eCamp\ContentType\ColumnLayout;

use Doctrine\ORM\ORMException;
use eCamp\Core\ContentType\ContentTypeStrategyBase;
use eCamp\Core\Entity\ContentNode;
use eCamp\Lib\Acl\NoAccessException;

class Strategy extends ContentTypeStrategyBase {
    public static array $DEFAULT_JSON_CONFIG = ['columns' => [
        ['slot' => '1', 'width' => 6],
        ['slot' => '2', 'width' => 6],
    ]];

    public function contentNodeExtract(ContentNode $contentNode): array {
        return [];
    }

    /**
     * @param ContentNode $contentNode
     */
    public function contentNodeCreated(ContentNode $contentNode): void {
        $contentNode->setJsonConfig(Strategy::$DEFAULT_JSON_CONFIG);
    }
}
