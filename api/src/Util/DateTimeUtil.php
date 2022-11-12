<?php

namespace App\Util;

class DateTimeUtil {
    public static function differenceInMinutes(\DateTimeInterface $from, \DateTimeInterface $to): int {
        return intval(floor(($to->getTimestamp() - $from->getTimestamp()) / 60));
    }
}
