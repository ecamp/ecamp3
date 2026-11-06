<?php

namespace App\Util;

use App\Entity\Activity;
use App\Entity\Period;
use App\Entity\ScheduleEntry;

trait GetPeriodsFromActivityTrait {
    /**
     * @return Period[]
     */
    private function getPeriodsFromActivity(Activity $activity): array {
        return array_values(array_filter(
            $activity->scheduleEntries->map(fn (ScheduleEntry $scheduleEntry) => $scheduleEntry->period)->getValues()
        ));
    }
}
