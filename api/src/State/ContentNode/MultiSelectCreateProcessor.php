<?php

namespace App\State\ContentNode;

use ApiPlatform\Metadata\Operation;
use App\Entity\ContentNode\MultiSelect;

class MultiSelectDataPersister extends ContentNodePersistProcessor {
    /**
     * @param MultiSelect $data
     */
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): MultiSelect {
        $data = parent::onBefore($data, $operation, $uriVariables, $context);

        // copy options from ContentType config
        $options = [];
        foreach ($data->contentType->jsonConfig['items'] as $item) {
            $options[$item] = [
                'checked' => false,
            ];
        }
        $data->data = ['options' => $options];

        return $data;
    }
}
