<?php

namespace App\DataPersister\ContentNode;

use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\ContentNode\ColumnLayout;

class ColumnLayoutDataPersister extends ContentNodeAbstractDataPersister
{
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
}
