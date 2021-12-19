<?php

namespace App\DataPersister;

use App\DataPersister\Util\AbstractDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\BaseEntity;
use App\Entity\Day;
use App\Entity\Period;

class PeriodDataPersister extends AbstractDataPersister {
    public function __construct(
        DataPersisterObservable $dataPersisterObservable
    ) {
        parent::__construct(
            Period::class,
            $dataPersisterObservable,
        );
    }

    /**
     * @param Period $data
     */
    public function beforeCreate($data): BaseEntity {
        $durationInDays = $this->getDurationInDays($data);

        // Create days
        for ($i = 0; $i < $durationInDays; ++$i) {
            $day = new Day();
            $day->dayOffset = $i;
            $data->addDay($day);
        }

        return $data;
    }

    private function getDurationInDays(Period $period) {
        if (null != $period->start && null != $period->end) {
            return $period->start->diff($period->end)->days + 1;
        }

        return 0;
    }
}
