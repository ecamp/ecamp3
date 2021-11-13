<?php

namespace App\DataPersister\ContentNode;

use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\ContentNode\SingleText;

class SingleTextDataPersister extends ContentNodeAbstractDataPersister {
    /**
     * @throws \ReflectionException
     */
    public function __construct(
        DataPersisterObservable $dataPersisterObservable
    ) {
        parent::__construct(
            SingleText::class,
            $dataPersisterObservable
        );
    }

    /**
     * @param SingleText $data
     */
    public function beforeCreate($data): SingleText {
        if (isset($data->prototype)) {
            if (!($data->prototype instanceof SingleText)) {
                throw new \Exception('Prototype must be of type SingleText');
            }

            if (!isset($data->text)) {
                $data->text = $data->prototype->text;
            }
        }

        return parent::beforeCreateContentNode($data);
    }
}
