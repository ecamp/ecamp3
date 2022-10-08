<?php

namespace App\DataPersister;

use App\DataPersister\Util\AbstractDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\Day;
use App\Entity\DayResponsible;
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
     */
    public function beforeCreate($data): Period {
        static::updateDaysAndScheduleEntries($data);

        return $data;
    }

    /**
     * @param Period $data
     */
    public function beforeUpdate($data): Period {
        $orig = $this->em->getUnitOfWork()->getOriginalEntityData($data);

        static::updateDaysAndScheduleEntries($data, $orig);

        return $data;
    }

    public static function updateDaysAndScheduleEntries(Period $period, array $orig = null) {
        $length = $period->getPeriodLength();
        $days = $period->getDays();
        $daysCount = count($days);

        if (0 == $daysCount) {
            // Create all Days:
            for ($i = 0; $i < $length; ++$i) {
                $day = new Day();
                $day->dayOffset = $i;
                $period->addDay($day);
            }
        } else {
            // Move Schedule-Entries
            if ($period->moveScheduleEntries) {
                // Add Days at end
                for ($i = $daysCount; $i < $length; ++$i) {
                    $day = new Day();
                    $day->dayOffset = $i++;
                    $period->addDay($day);
                }
                // Remove Days at end
                for ($i = $daysCount; $i > $length; --$i) {
                    $day = $days[$i - 1];
                    $period->removeDay($day);
                }
            } else {
                $deltaMinutes = DateTimeUtil::differenceInMinutes($period->start, $orig['start']);
                $deltaDaysAtStart = floor($deltaMinutes / 60 / 24);

                // Tage erstellen
                for ($i = $daysCount; $i < $length; ++$i) {
                    $day = new Day();
                    $day->dayOffset = $i;
                    $period->addDay($day);
                }

                // Move ScheduleEntries
                foreach ($period->scheduleEntries as $scheduleEntry) {
                    $scheduleEntry->startOffset += $deltaMinutes;
                    $scheduleEntry->endOffset += $deltaMinutes;
                }

                // Move DayResponsibles
                foreach ($period->days as $day) {
                    /** @var Day $day */
                    $newDay = $period->days->filter(fn ($d) => $d->dayOffset == $day->dayOffset + $deltaDaysAtStart)->first();

                    /** @var DayResponsible $dayResp */
                    foreach ($day->dayResponsibles as $dayResp) {
                        if (null != $newDay) {
                            $dayResp->day = $newDay;
                        } else {
                            $day->removeDayResponsible($dayResp);
                        }
                    }
                }

                // Remove Days at end
                for ($i = $daysCount; $i > $length; --$i) {
                    $day = $days[$i - 1];
                    $period->removeDay($day);
                }
            }
        }
    }
}
