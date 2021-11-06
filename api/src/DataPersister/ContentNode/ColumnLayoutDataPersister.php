<?php

namespace App\DataPersister\ContentNode;

use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\ContentNode\ColumnLayout;

class ColumnLayoutDataPersister extends ContentNodeAbstractDataPersister {
    /**
     * @throws \ReflectionException
     */
    public function __construct(
        DataPersisterObservable $dataPersisterObservable
    ) {
        parent::__construct(
            ColumnLayout::class,
            $dataPersisterObservable
        );
    }

    /**
     * @param ColumnLayout $data
     */
    public function beforeCreate($data): ColumnLayout {
        if (isset($data->prototype)) {
            if (!($data->prototype instanceof ColumnLayout)) {
                throw new \Exception('Prototype must be of type ColumnLayout');
            }

            $data->copyFromPrototype($data->prototype);
        }

        return parent::beforeCreate($data);
    }
}
