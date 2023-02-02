<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Day;
use App\Entity\DayResponsible;
use App\Entity\Period;
use App\State\Util\AbstractPersistProcessor;
use App\Util\DateTimeUtil;

class PeriodPersistProcessor extends AbstractPersistProcessor {
    public function __construct(
        ProcessorInterface $decorated
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param Period $data
     */
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): Period {
        static::updateDaysAndScheduleEntries($data, $context['previous_data'] ?? null);

        return $data;
    }

    public static function updateDaysAndScheduleEntries(Period $period, Period $orig = null) {
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
                    $day->dayOffset = $i;
                    $period->addDay($day);
                }
                // Remove Days at end
                for ($i = $daysCount - 1; $i >= $length; --$i) {
                    $day = $days[$i];
                    $period->removeDay($day);
                }
            } else {
                $deltaMinutes = DateTimeUtil::differenceInMinutes($period->start, $orig->start);
                $deltaDaysAtStart = floor($deltaMinutes / 60 / 24);

                // Add days
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
                $days = $period->days->getValues();
                usort($days, fn ($a, $b) => $a->dayOffset <=> $b->dayOffset);

                // UniqueIndex day_campCollaboration_unique forces correct order of Update
                if ($deltaDaysAtStart > 0) {
                    $days = array_reverse($days);
                }

                foreach ($days as $day) {
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
                for ($i = $daysCount - 1; $i >= $length; --$i) {
                    $day = $days[$i];
                    $period->removeDay($day);
                }
            }
        }
    }
}
