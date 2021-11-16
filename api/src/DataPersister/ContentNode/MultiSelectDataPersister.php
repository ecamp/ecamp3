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
     * @param MultiSelect $multiSelect
     */
    public function beforeCreate($multiSelect): MultiSelect {
        parent::beforeCreate($multiSelect);

        // no prototype given --> copy from ContentType config
        if (!isset($multiSelect->prototype)) {
            foreach ($multiSelect->contentType->jsonConfig['items'] as $key => $item) {
                $option = new MultiSelectOption();

                $option->translateKey = $item;
                $option->setPos($key);

                $multiSelect->addOption($option);
            }
        }

        return $multiSelect;
    }
}
