<?php

namespace App\DataPersister;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\DataPersister\Util\AbstractDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\BaseEntity;
use App\Entity\Period;

class PeriodDataPersister extends AbstractDataPersister {
    public function __construct(
        DataPersisterObservable $dataPersisterObservable,
        private ValidatorInterface $validator
    ) {
        parent::__construct(
            Period::class,
            $dataPersisterObservable,
        );
    }

    public function beforeRemove($data): ?BaseEntity {
        $this->validator->validate($data, ['groups' => ['delete', 'Period:delete']]);

        return null;
    }
}
