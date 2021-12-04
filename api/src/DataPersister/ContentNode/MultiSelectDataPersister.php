<?php

namespace App\DataPersister\ContentNode;

use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\ContentNode\MultiSelect;
use App\Entity\ContentNode\MultiSelectOption;

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
        parent::beforeCreate($data);

        // copy options from ContentType config
        foreach ($data->contentType->jsonConfig['items'] as $key => $item) {
            $option = new MultiSelectOption();

            $option->translateKey = $item;
            $option->setPos($key);

            $data->addOption($option);
        }

        return $data;
    }
}
