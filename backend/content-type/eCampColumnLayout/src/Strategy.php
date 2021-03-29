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

    public function contentNodeExtract(ContentNode $contentNode): array {
        return [];
    }

    public function contentNodeCreated(ContentNode $contentNode): void {
        $contentNode->setJsonConfig(Strategy::$DEFAULT_JSON_CONFIG);
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
                                'pattern' => '^[1-9][0-9]*$'
                            ],
                            'width' => [
                                'type' => 'integer',
                                'minimum' => 1, 'maximum' => 12
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function validateContentNode(ContentNode $contentNode): void {
        parent::validateContentNode($contentNode);

        $columnWidths = array_sum(array_map(function ($col) {
            return $col['width'];
        }, $contentNode->getJsonConfig()['columns']));
        if ($columnWidths !== 12) {
            throw (new EntityValidationException())->setMessages([
               'jsonConfig' => [
                   'invalidWidths' => 'Expected column widths to sum to 12, but got a sum of ' . $columnWidths
               ]
            ]);
        }
    }
}
