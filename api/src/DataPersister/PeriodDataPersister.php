<?php

namespace App\DataPersister;

use App\DataPersister\Util\AbstractDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\Day;
use App\Entity\Period;
use App\Util\DateTimeUtil;
use Doctrine\ORM\EntityManagerInterface;

class PeriodDataPersister extends AbstractDataPersister {
    public function __construct(
        DataPersisterObservable $dataPersisterObservable,
        private EntityManagerInterface $em
    ) {
        parent::__construct(
            Period::class,
            $dataPersisterObservable,
        );
    }

    /**
     * @param Period $data
     *
     * @return Period
     */
    public function beforeCreate($data) {
        static::updateDaysAndScheduleEntries($data);

        return $data;
    }

    /**
     * @param Period $data
     *
     * @return Period
     */
    public function beforeUpdate($data) {
        $orig = $this->em->getUnitOfWork()->getOriginalEntityData($data);

        static::updateDaysAndScheduleEntries($data, $orig);

        return $data;
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
                $deltaMinutes = DateTimeUtil::differenceInMinutes($period->start, $orig['start']);
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
