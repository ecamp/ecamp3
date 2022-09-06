<?php

namespace App\DataPersister\ContentNode;

use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\ContentNode\MultiSelect;

class MultiSelectDataPersister extends ContentNodeAbstractDataPersister {
    /**
     * @throws \ReflectionException
     */
    public function __construct(
        DataPersisterObservable $dataPersisterObservable
    ) {
        parent::__construct(
            MultiSelect::class,
            $dataPersisterObservable
        );
    }

    /**
     * @param MultiSelect $data
     */
    public function beforeCreate($data): MultiSelect {
        $data = parent::beforeCreate($data);

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
