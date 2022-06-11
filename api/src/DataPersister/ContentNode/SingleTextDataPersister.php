<?php

namespace App\DataPersister\ContentNode;

use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\ContentNode\SingleText;
use App\InputFilter\CleanHTMLFilter;

class SingleTextDataPersister extends ContentNodeAbstractDataPersister {
    /**
     * @throws \ReflectionException
     */
    public function __construct(
        DataPersisterObservable $dataPersisterObservable,
        private CleanHTMLFilter $cleanHTMLFilter
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
        $data = parent::beforeCreate($data);

        $data->data = ['text' => ''];

        return $data;
    }

    /**
     * @param SingleText $data
     */
    public function beforeUpdate($data): SingleText {
        $data = parent::beforeUpdate($data);

        $data->data = $this->cleanHTMLFilter->applyTo($data->data, 'text');

        return $data;
    }
}
