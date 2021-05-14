<?php

namespace eCamp\ContentType\ColumnLayout;

use eCamp\Core\ContentType\ContentTypeStrategyBase;
use eCamp\Core\Entity\ContentNode;
use eCamp\Lib\Service\EntityValidationException;

class Strategy extends ContentTypeStrategyBase {
    public static array $DEFAULT_JSON_CONFIG = ['columns' => [
        ['slot' => '1', 'width' => 6],
        ['slot' => '2', 'width' => 6],
    ]];
    public static array $SINGLE_COLUMN_JSON_CONFIG = ['columns' => [
        ['slot' => '1', 'width' => 12],
    ]];

    public function contentNodeExtract(ContentNode $contentNode): array {
        return [];
    }

    public function contentNodeCreated(ContentNode $contentNode, ?ContentNode $prototype = null, ?array $jsonConfig = null): void {
        if (null !== $jsonConfig) {
            $contentNode->setJsonConfig($jsonConfig);
        } elseif (null !== $prototype) {
            $contentNode->setJsonConfig($prototype->getJsonConfig());
        } else {
            $contentNode->setJsonConfig(Strategy::$DEFAULT_JSON_CONFIG);
        }
    }

    public function validateContentNode(ContentNode $contentNode): void {
        parent::validateContentNode($contentNode);

        $this->validateColumWidthsSumTo12($contentNode);
        $this->validateNoOrphanChildren($contentNode);
    }

    protected function getJsonConfigSchema(): array {
        return [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['columns'],
            'properties' => [
                'columns' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'additionalProperties' => false,
                        'required' => ['slot', 'width'],
                        'properties' => [
                            'slot' => [
                                'type' => 'string',
                                'pattern' => '^[1-9][0-9]*$',
                            ],
                            'width' => [
                                'type' => 'integer',
                                'minimum' => 3,
                                'maximum' => 12,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function validateColumWidthsSumTo12(ContentNode $contentNode) {
        $columnWidths = array_sum(array_map(function ($col) {
            return $col['width'];
        }, $contentNode->getJsonConfig()['columns']));
        if (12 !== $columnWidths) {
            throw (new EntityValidationException())->setMessages([
                'jsonConfig' => [
                    'invalidWidths' => 'Expected column widths to sum to 12, but got a sum of '.$columnWidths,
                ],
            ]);
        }
    }

    protected function validateNoOrphanChildren(ContentNode $contentNode) {
        $slots = array_map(function ($col) {
            return $col['slot'];
        }, $contentNode->getJsonConfig()['columns']);
        $childSlots = $contentNode->getChildren()->map(function (ContentNode $child) {
            return $child->getSlot();
        })->toArray();
        $orphans = array_diff($childSlots, $slots);

        if (count($orphans)) {
            throw (new EntityValidationException())->setMessages([
                'jsonConfig' => [
                    'orphanChildContents' => 'The following slots still have child contents and should be present in the columns: '.join(', ', $orphans),
                ],
            ]);
        }
    }
}
