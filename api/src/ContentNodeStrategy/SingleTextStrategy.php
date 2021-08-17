<?php

namespace App\ContentNodeStrategy;

use App\Entity\ContentNode;

class SingleTextStrategy {
    public function contentNodeExtract(ContentNode $contentNode): array {
        return [
            'strategyType' => 'singleText',
            'contentNodeId' => $contentNode->getId(),
        ];
    }
}
