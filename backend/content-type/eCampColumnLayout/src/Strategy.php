<?php

namespace eCamp\ContentType\ColumnLayout;

use Doctrine\ORM\ORMException;
use eCamp\Core\ContentType\ContentTypeStrategyBase;
use eCamp\Core\Entity\ContentNode;
use eCamp\Lib\Acl\NoAccessException;

class Strategy extends ContentTypeStrategyBase {
    public static $DEFAULT_JSON_CONFIG = ['columns' => [
        ['slot' => '1'],
        ['slot' => '2'],
    ]];

    public function contentNodeExtract(ContentNode $contentNode): array {
        return [];
    }

    /**
     * @throws NoAccessException
     * @throws ORMException
     */
    public function contentNodeCreated(ContentNode $contentNode): void {
        $contentNode->setJsonConfig(Strategy::$DEFAULT_JSON_CONFIG);
    }
}
