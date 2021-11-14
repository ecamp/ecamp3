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
        $this->setRootFromParent($multiSelect);

        if (isset($multiSelect->prototype)) {
            // copy from Prototype

            if (!($multiSelect->prototype instanceof MultiSelect)) {
                throw new \Exception('Prototype must be of type MultiSelect');
            }

            $multiSelect->copyFromPrototype($multiSelect->prototype);
        } else {
            // no prototype given --> copy from ContentType config

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
