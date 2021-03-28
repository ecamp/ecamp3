<?php

namespace eCamp\ContentType\ColumnLayout;

use eCamp\Core\ContentType\ContentTypeStrategyBase;
use eCamp\Core\Entity\ContentNode;

class Strategy extends ContentTypeStrategyBase {
    public static array $DEFAULT_JSON_CONFIG = ['columns' => [
        ['slot' => '1', 'width' => 6],
        ['slot' => '2', 'width' => 6],
    ]];

    public function contentNodeExtract(ContentNode $contentNode): array {
        return [];
    }

    public function contentNodeCreated(ContentNode $contentNode): void {
        $contentNode->setJsonConfig(Strategy::$DEFAULT_JSON_CONFIG);
    }
}
