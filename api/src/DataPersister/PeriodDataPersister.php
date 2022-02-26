<?php

namespace App\DataPersister;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\DataPersister\Util\AbstractDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\BaseEntity;
use App\Entity\Day;
use App\Entity\Period;
use Doctrine\ORM\EntityManagerInterface;

class PeriodDataPersister extends AbstractDataPersister {
    public function __construct(
        DataPersisterObservable $dataPersisterObservable,
        private EntityManagerInterface $em,
        private ValidatorInterface $validator
    ) {
        parent::__construct(
            Period::class,
            $dataPersisterObservable,
        );
    }

    public function beforeCreate($data): BaseEntity {
        static::updateDaysAndScheduleEntries($data);

        return $data;
    }

    public function beforeUpdate($data): BaseEntity {
        $orig = $this->em->getUnitOfWork()->getOriginalEntityData($data);

        static::updateDaysAndScheduleEntries($data, $orig);

        return $data;
    }

    public function beforeRemove($data): ?BaseEntity {
        $this->validator->validate($data, ['groups' => ['delete', 'Period:delete']]);

        return null;
    }

    public static function updateDaysAndScheduleEntries(Period $period, array $orig = null) {
        $length = $period->getPeriodLength();
        $days = $period->getDays();

        // Add Days
        $i = count($days);
        while ($i < $length) {
            $day = new Day();
            $day->dayOffset = $i++;
            $period->addDay($day);
        }

        // Move Schedule-Entries
        if (!$period->moveScheduleEntries) {
            $deltaMinutes = 0;
            if (null != $orig) {
                $deltaMinutes = $orig['start']->getTimestamp() - $period->start->getTimestamp();
                $deltaMinutes = floor($deltaMinutes / 60);
            }

            foreach ($period->scheduleEntries as $scheduleEntry) {
                $scheduleEntry->startOffset += $deltaMinutes;
                $scheduleEntry->endOffset += $deltaMinutes;
            }
        }

        // Remove Days
        $i = count($days);
        while ($i > $length) {
            $day = $days[--$i];
            $period->removeDay($day);
        }
    }
}
